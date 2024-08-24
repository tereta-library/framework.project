<?php declare(strict_types=1);

namespace Builder\Menu\Api;

use Builder\Menu\Helper\Converter as MenuConverter;
use Builder\Site\Api\Abstract\Admin as AdminAbstract;
use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Exception;
use Framework\Api\Interface\Api;
use Framework\Application\Manager\Http\Parameter\Payload as PayloadPost;
use Framework\Repository as MenuRepository;
use Builder\Menu\Model\Resource\Menu\Item\Collection as MenuItemCollection;
use Builder\Menu\Model\Resource\Menu\Item as MenuItemResource;
use Builder\Menu\Model\Resource\Menu as MenuResource;
use Builder\Menu\Model\Menu as MenuModel;

/**
 * @class Builder\Menu\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    private MenuResource $menuResource;

    private MenuItemResource $menuItemResource;

    public function __construct()
    {
        $this->menuResource = new MenuResource;
        $this->menuItemResource = new MenuItemResource;
    }

    /**
     * @param string $identifier
     * @return array
     * @throws Exception
     * @api GET /^menu\/configuration\/([a-zA-Z0-9_-]+)$/Usi
     */
    public function getConfiguration(string $identifier): array
    {
        $menuModel = MenuRepository::getInstance()->getByIdentifier(
            $identifier,
            $this->siteModel->get('id')
        );

        return [
            'identifier' => $identifier,
            'menu' => MenuConverter::toArray($menuModel->getListing())
        ];
    }

    /**
     * @param string $identifier
     * @param PayloadPost $payload
     * @return array
     * @throws Exception
     * @api POST /^menu\/configuration\/([a-zA-Z0-9_-]+)$/Usi
     */
    public function setConfiguration(string $identifier, PayloadPost $payload): array
    {
        $menuModel = MenuRepository::getInstance()->getByIdentifier(
            $identifier,
            $this->siteModel->get('id')
        );

        $removeIds = $payload->get('removeIds');

        // Save section
        $tree = MenuConverter::toObject($payload->get('menu'));
        $listing = MenuConverter::fetchAll($tree);

        $toSave = [];
        $checkIds = [];

        foreach ($listing as $menuItem)
        {
            if ($menuItem->get('id') && !$menuItem->get('isEdited')) {
                continue;
            }

            $menuItem->set('menuId', $menuModel->get('id'));
            $toSave[] = $menuItem;

            if ($menuItem->get('parentId')) {
                $checkIds[] = $menuItem->get('parentId');
            }

            if ($menuItem->get('id')) {
                $checkIds[] = $menuItem->get('id');
            }

            if ($menuItem->getParent()) {
                $menuItem->set('parentId', $menuItem->getParent()->get('id'));
            }
        }

        // Security check
        $menuItemCollection = [];
        if ($checkIds = array_merge($checkIds, $removeIds)) {
            $menuItemCollection = (new MenuItemCollection)->where(
                'id IN (' . implode(', ', array_map('intval', $checkIds)) . ')'
            );
        }
        foreach ($menuItemCollection as $menuItem) {
            if ($menuItem->get('menuId') != $menuModel->get('id')) {
                throw new Exception('Menu declined by security reason.');
            }
        }

        // Save & return
        $savedModel = [];
        $clientIdMap = [];
        foreach ($toSave as $menuItem) {
            $this->menuItemResource->save($menuItem);
            $clientIdMap[$menuItem->get('clientId')] = $menuItem->get('id');
            $savedModel[] = $menuItem;
        }

        $return = [];
        foreach ($savedModel as $menuItem) {
            if (!$menuItem->get('parentId')) {
                $clientId = $menuItem->get('clientId');
                $menuItem->set('parentId', $clientIdMap[$menuItem->get('parentClientId')]);
                $this->menuItemResource->save($menuItem);
                $this->menuItemResource->load($menuItem);
                $menuItem->set('clientId', $clientId);
            }
            $return[] = $menuItem->getData();
        }

        // Delete section
        if ($removeIds) {
            $this->menuItemResource->delete($removeIds);
        }

        return $return;
    }
}