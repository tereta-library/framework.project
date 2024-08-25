<?php declare(strict_types=1);

namespace Builder\Page\Api;

use Framework\Api\Interface\Api;
use Builder\Page\Model\Resource\Url\Collection as ModelUrlCollection;
use Builder\Page\Model\Type\Repository as TypeRepository;
use Exception;

/**
 * @class Builder\Page\Api\Listing
 * @package Builder\Page
 */
class Listing implements Api
{
    /**
     * @return array
     * @throws Exception
     * @api POST /^page\/getListing$/Usi
     */
    public function getListing(): array
    {
        $modelUrlCollection = new ModelUrlCollection;
        $urlList = [];
        $byType = [];
        foreach ($modelUrlCollection as $item) {
            $urlList[] = $item;
            $byType[$item->get('id')][] = $item;
        }

        $result = [];
        foreach($byType as $typeId => $item) {
            $this->getListingPrepare($typeId, $item, $result);
        }

        return $result;
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
            $collectIds[] = $urlModel->get('id');
        }

        $typeCollectionName = $typeModel->get('identifier');
        $typeCollection = new $typeCollectionName;
        $typeCollection->filterByIdentifiers($collectIds);
        $collectionById = [];
        foreach($typeCollection as $item) {
            $collectionById[$item->get('id')] = $item;
        }

        foreach($urlModelList as $urlModel) {
            $model = $collectionById[$urlModel->get('id')];
            $result[] = [
                'id' => $model->get('id'),
                'title' => $model->get('title'),
                'uri' => $urlModel->get('uri'),
                'url' => $urlModel->get('uri'),
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