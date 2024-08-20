<?php declare(strict_types=1);

namespace Builder\Menu\Model\Menu;

use Builder\Menu\Model\Resource\Menu as ResourceMenu;
use Builder\Menu\Model\Menu as MenuModel;
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
     * @var Repository|null $instance
     */
    protected static ?self $instance = null;

    /**
     * @var array $registeredKeys
     */
    protected array $registeredKeys = ['id', ['siteId', 'identifier']];

    /**
     * @throws Exception
     */
    protected function __construct() {
        $this->resource = new ResourceMenu;
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
            'siteId' => $data['siteId'],
            'identifier' => $data['identifier'],
        ]);
        $this->resource->save($menuModel);

        return $menuModel;
    }
}