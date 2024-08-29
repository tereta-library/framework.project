<?php declare(strict_types=1);

namespace Builder\Content\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Framework\Application\Manager\Http\Parameter\Payload;
use Builder\Content\Model\Content as ContentModel;
use Builder\Content\Model\Resource\Content as ContentResource;
use Exception;
use PDOException;

/**
 * @class Builder\Content\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    /**
     * @api GET /^content\/get\/([a-zA-Z0-9_-]+)?$/Usi
     * @param string|null $identifier
     * @return array
     */
    public function getContent(?string $identifier = null): array
    {
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

    /**
     * @param Payload $payload
     * @return array
     * @throws Exception
     * @api POST /^content\/save$/Usi
     */
    public function saveContent(Payload $payload): array
    {
        $this->securityCheck((int) $payload->get('id'));

        $contentModel = new ContentModel($payload->getData());
        $contentModel->set('siteId', $this->siteModel->get('id'));
        $contentModel->set('identifier', $payload->get('identifier') ?? '');
        $contentModel->set('status', $payload->get('status') ? 1 : 0);
        $contentModel->set('seoUri', $payload->get('seoUri') ?? '');
        $contentModel->set('seoTitle', $payload->get('seoTitle') ?? '');
        $contentModel->set('header', $payload->get('header') ?? '');
        $contentModel->set('description', $payload->get('description') ?? '');
        $contentModel->set('content', $payload->get('content') ?? '');

        try {
            ContentResource::getInstance()->save($contentModel);
        } catch (PDOException $e) {
            switch($e->getCode()) {
                case 23000:
                    throw new Exception('Identifier or url key already exists');
                default:
                    throw new Exception('Error saving content');
            }
        }

        return $contentModel->getData();
    }

    /**
     * @param int|null $id
     * @return void
     * @throws Exception
     */
    private function securityCheck(?int $id): void
    {
        if (!$id) {
            return;
        }

        ContentResource::getInstance()->load($contentModel = new ContentModel(['id' => $id]));

        if ($contentModel->get('siteId') !== $this->siteModel->get('id')) {
            throw new Exception('Access denied');
        }
    }
}