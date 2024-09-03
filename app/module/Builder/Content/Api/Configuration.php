<?php declare(strict_types=1);

namespace Builder\Content\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Framework\Application\Manager\Http\Parameter\Payload;
use Builder\Content\Model\Content as ContentModel;
use Builder\Content\Model\Resource\Content as ContentResource;
use Exception;
use Framework\Database\Singleton as SingletonDatabase;
use Framework\Helper\File as FileHelper;
use PDOException;
use Builder\Page\Model\Url as UrlModel;
use Builder\Page\Model\Url\Repository as UrlRepository;
use Builder\Content\Model\Url as ContentUrlModel;
use Builder\Page\Model\Resource\Url as UrlResource;;

/**
 * @class Builder\Content\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    /**
     * @var UrlResource $urlResource
     */
    private UrlResource $urlResource;

    /**
     * @var ContentResource $contentResource
     */
    private ContentResource $contentResource;

    /**
     * @var UrlRepository $urlRepository
     */
    private UrlRepository $urlRepository;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->urlResource = new UrlResource;
        $this->contentResource = new ContentResource;
        $this->urlRepository = new UrlRepository;
    }

    /**
     * @api GET /^content\/get\/([a-zA-Z0-9_-]+)?$/Usi
     * @param string|null $identifier
     * @return array
     * @throws Exception
     */
    public function getContent(?string $identifier = null): array
    {
        if (!$identifier) {
            return [
                'id'          => null,
                'identifier'  => null,
                'seoUri'      => null,
                'seoTitle'    => null,
                'title'       => null,
                'description' => null,
                'content'     => null,
            ];
        }

        $this->contentResource->load($contentModel = new ContentModel(), [
            'identifier' => $identifier,
            'siteId'     => $this->siteModel->get('id'),
        ]);

        return $contentModel->getData();
    }

    /**
     * @param Payload $payload
     * @return array
     * @throws Exception
     * @api POST /^content\/remove$/Usi
     */
    public function removeContent(Payload $payload): array
    {
        $id = (int) $payload->get('id');
        $this->contentResource->load($contentModel = new ContentModel(['id' => $id]));

        $this->securityCheck($contentModel);

        UrlRepository::getInstance()->loadUrl($urlModel = new UrlModel([
            'siteId' => $this->siteModel->get('id'),
            'typeClass' => ContentUrlModel::class,
            'identifier' => $contentModel->get('id'),
        ]));

        SingletonDatabase::getConnection()->beginTransaction();
        try {
            $filePath = "content/{$contentModel->get('id')}/";
            $fullPath = $this->siteModel->getMedia()->getPath($filePath);

            $this->urlResource->delete($urlModel);
            $this->contentResource->delete($id);

            FileHelper::getInstance()->remove($fullPath);

            SingletonDatabase::getConnection()->commit();
        } catch (PDOException $e) {
            SingletonDatabase::getConnection()->rollBack();
            throw $e;
        }

        return $contentModel->getData();
    }

    /**
     * @param Payload $payload
     * @return array
     * @throws Exception
     * @api POST /^content\/save$/Usi
     */
    public function saveContent(Payload $payload): array
    {
        $this->securityCheck((int) $payload->get('id'));

        if (!$payload->get('seoUri')) {
            throw new Exception('SEO URI is required');
        }

        $contentModel = new ContentModel($payload->getData());
        $contentModel->set('siteId', $this->siteModel->get('id'));
        $contentModel->set('identifier', $payload->get('identifier') ?? '');
        $contentModel->set('status', $payload->get('status') ? 1 : 0);
        $contentModel->set('seoUri', str_starts_with($payload->get('seoUri'), '/') ? $payload->get('seoUri') : '/' . $payload->get('seoUri'));
        $contentModel->set('seoTitle', $payload->get('seoTitle') ?? '');
        $contentModel->set('header', $payload->get('header') ?? '');
        $contentModel->set('description', $payload->get('description') ?? '');
        $contentModel->set('content', $payload->get('content') ?? '');

        try {
            SingletonDatabase::getConnection()->beginTransaction();
            ContentResource::getInstance()->save($contentModel);

            // Save URL rewrite
            UrlRepository::getInstance()->saveUrl($urlModel = new UrlModel([
                'siteId' => $this->siteModel->get('id'),
                'typeId' => ContentUrlModel::class,
                'identifier' => $contentModel->get('id'),
                'uri' => $contentModel->get('seoUri'),
            ]));

            SingletonDatabase::getConnection()->commit();
        } catch (PDOException $e) {
            SingletonDatabase::getConnection()->rollBack();
            switch($e->getCode()) {
                case 23000:
                    throw new Exception('Identifier or url key already exists');
                default:
                    throw new Exception('Error saving content');
            }
        }

        return [
            'content' => $contentModel->getData(),
            'url'     => $urlModel->getData(),
        ];
    }

    /**
     * @param int|ContentModel|null $id
     * @return void
     * @throws Exception
     */
    private function securityCheck(int|ContentModel|null $id): void
    {
        if (!$id) {
            return;
        }

        if (is_int($id)) {
            ContentResource::getInstance()->load($contentModel = new ContentModel(['id' => $id]));
        } else {
            $contentModel = $id;
        }

        if ($contentModel->get('siteId') !== $this->siteModel->get('id')) {
            throw new Exception('Access denied');
        }
    }
}