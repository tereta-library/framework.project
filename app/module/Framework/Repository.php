<?php declare(strict_types=1);

namespace Framework;

use Builder\Menu\Helper\Converter as MenuConverter;
use Builder\Menu\Model\Menu as MenuModel;
use Builder\Menu\Model\Resource\Menu as ResourceMenu;
use Builder\Menu\Model\Resource\Menu\Item as ResourceMenuItem;
use Builder\Menu\Model\Resource\Menu\Item\Collection as ItemCollection;
use Exception;
use Framework\Database\Abstract\Repository as AbstractRepository;

/**
 * @class Builder\Menu\Model\Menu\Repository
 */
class Repository extends AbstractRepository
{
    /**
     * @var ResourceMenu $resource
     */
    private ResourceMenu $resource;

    /**
     * @var ResourceMenuItem $resourceItem
     */
    private ResourceMenuItem $resourceItem;

    /**
     * @var array $registeredKeys
     */
    protected array $registeredKeys = ['id', ['siteId', 'identifier']];

    /**
     * @throws Exception
     */
    protected function __construct() {
        $this->resource = new ResourceMenu;
        $this->resourceItem = new ResourceMenuItem;
    }

    /**
     * @param string $identifier
     * @param int $siteId
     * @return MenuModel
     * @throws Exception
     */
    public function getByIdentifier(string $identifier, int $siteId): MenuModel
    {
        if ($registeredModel = $this->getRegisterModel(['siteId' => $siteId, 'identifier' => $identifier])) {
            return $registeredModel;
        }

        // MenuModel is a class that extends Model
        $this->resource->load($menuModel = new MenuModel, ['siteId' => $siteId, 'identifier' => $identifier]);

        if (!$menuModel->get('id')) {
            throw new Exception('Menu not found');
        }

        $itemCollection = (new ItemCollection)->load($menuModel->get('id'), 'menuId');
        $itemCollectionMap = [];
        foreach ($itemCollection as $itemModel) {
            $itemCollectionMap[$itemModel->get('id')] = $itemModel;
        }

        $rootListing = [];
        $subMenuArray = [];
        foreach ($itemCollectionMap as $itemModel) {
            if (!$itemModel->get('parentId')) {
                $rootListing[] = $itemModel;
                continue;
            }

            if (!isset($subMenuArray[$itemModel->get('parentId')])) {
                $subMenuArray[$itemModel->get('parentId')] = [];
            }

            $subMenuArray[$itemModel->get('parentId')][] = $itemModel;
            $itemModel->setParent($itemCollectionMap[$itemModel->get('parentId')]);
        }

        foreach ($subMenuArray as $key => $subMenu) {
            $itemCollectionMap[$key]->setMenu($subMenu);
        }

        $menuModel->setListing($rootListing);

        $this->setRegisterModel($menuModel);

        return $menuModel;
    }

    /**
     * @param array $data
     * @return MenuModel
     * @throws Exception
     */
    public function create(array $data): MenuModel
    {
        $menuModel = new MenuModel;
        $menuModel->setData([
            'siteId'     => $data['siteId'],
            'identifier' => $data['identifier'],
            'label'      => $data['label'],
        ]);
        $this->resource->save($menuModel);

        $menuData = json_decode($data['data'], true);
        $menuTree = MenuConverter::toObject($menuData);
        $menuModel->setListing($menuTree);

        $objectList = MenuConverter::fetchAll($menuTree);
        foreach ($objectList as $object) {
            $object->set('parentId', $object->getParent()?->get('id') ?? null);
            $object->set('menuId', $menuModel->get('id') ?? null);
            $this->resourceItem->save($object);
        }

        return $menuModel;
    }
}