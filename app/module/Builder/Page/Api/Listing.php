<?php declare(strict_types=1);

namespace Builder\Page\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Builder\Page\Model\Resource\Url\Collection as ModelUrlCollection;
use Builder\Page\Model\Type\Repository as TypeRepository;
use Exception;
use Framework\Application\Manager\Http\Parameter\Post as PostData;

/**
 * @class Builder\Page\Api\Listing
 * @package Builder\Page
 */
class Listing implements Api
{
    use AdministratorTrait;

    /**
     * @param PostData $post
     * @return array
     * @throws Exception
     * @api POST /^page\/getListing$/Usi
     */
    public function getListing(PostData $post): array
    {
        $modelUrlCollection = new ModelUrlCollection;
        $modelUrlCollection->where('siteId', $this->siteModel->get('id'));
        $totalUrls = $modelUrlCollection->getSize();
        $limitPerPage = 10;

        $modelUrlCollection = new ModelUrlCollection;
        $modelUrlCollection->where('siteId', $this->siteModel->get('id'));
        $modelUrlCollection->setLimit($limitPerPage);
        $modelUrlCollection->setPage((int) $post->get('page'));

        $byType = [];
        foreach ($modelUrlCollection as $item) {
            $byType[$item->get('typeId')][] = $item;
        }

        $listing = [];
        foreach($byType as $typeId => $item) {
            $this->getListingPrepare($typeId, $item, $listing);
        }

        return [
            'config' => [
                'page' => $post->get('page'),
                'perPage' => $limitPerPage,
                'totalPages' => ceil($totalUrls / $limitPerPage),
            ],
            'listing' => $listing
        ];
    }

    /**
     * @param int $typeId
     * @param array $urlModelList
     * @param array $result
     * @return void
     * @throws Exception
     */
    private function getListingPrepare(int $typeId, array $urlModelList, array &$result): void
    {
        $typeModel = TypeRepository::getInstance()->getTypeById($typeId);

        $collectIds = [];
        foreach($urlModelList as $urlModel) {
            $collectIds[] = $urlModel->get('identifier');
        }

        $typeCollectionName = $typeModel->get('identifier');
        $typeCollection = new $typeCollectionName;
        $collection = $typeCollection->getCollectionByIdentifiers($collectIds);
        $collectionById = [];
        foreach($collection as $item) {
            $collectionById[$item['id']] = $item;
        }

        foreach($urlModelList as $urlModel) {
            if (!isset($collectionById[$urlModel->get('identifier')])) {
                $result[] = [
                    'id' => $urlModel->get('id'),
                    'title' => 'Deleted',
                    'uri' => $urlModel->get('uri'),
                ];
                continue;
            }

            $model = $collectionById[$urlModel->get('identifier')];
            $result[] = [
                'id' => $urlModel->get('id'),
                'title' => $model['title'],
                'uri' => $urlModel->get('uri'),
            ];
        }
    }

    /**
     * @return array
     * @api GET /^page\/create/Usi
     */
    public function create(): array
    {
        return [];
    }
}