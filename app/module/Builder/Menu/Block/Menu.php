<?php declare(strict_types=1);

namespace Builder\Menu\Block;

use Builder\Menu\Helper\Converter as MenuConverter;
use Framework\View\Php\Template;
use Builder\Menu\Model\Menu\Repository as MenuRepository;
use Exception;

/**
 * @class Builder\Menu\Block\Menu
 */
class Menu extends Template
{
    /**
     * @return void
     * @throws Exception
     */
    protected function construct(): void
    {
        try {
            $menuModel = MenuRepository::getInstance()->getByIdentifier($this->get('identifier'), 1);
        } catch (Exception $e) {
            $menuModel = MenuRepository::getInstance()->create([
                'siteId' => 1,
                'identifier' => $this->get('identifier'),
                'data' => json_encode([
                    ['label' => 'Home', 'link' => '/'],
                    ['label' => 'About', 'link' => '/about', 'menu' => [
                        ['label' => 'History', 'link' => '/about/history'],
                        ['label' => 'Vision', 'link' => '/about/vision', 'menu' => [
                            ['label' => 'Submenu 1', 'link' => '/about/vision/submenu1'],
                            ['label' => 'Submenu 2', 'link' => '/about/vision/submenu2'],
                        ]],
                        ['label' => 'Mission', 'link' => '/about/mission'],
                    ]],
                    ['label' => 'Service', 'link' => '/service'],
                    ['label' => 'Product', 'link' => '/product'],
                    ['label' => 'Contact', 'link' => '/contact'],
                ]),
            ]);
        }

        $list = MenuConverter::toArray($menuModel->getListing());

        $this->assign('dataAdmin', "@dataAdmin " . json_encode(['menu' => 'default']));
        $this->assign('list', $list);
    }
}