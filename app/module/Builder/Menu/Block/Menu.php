<?php declare(strict_types=1);

namespace Builder\Menu\Block;

use Framework\View\Php\Template;

/**
 * @class Builder\Menu\Block\Menu
 */
class Menu extends Template
{
    protected function construct(): void
    {
        $dataAdmin = ['menu' => 'default'];
        $this->assign('dataAdmin', "@dataAdmin " . json_encode($dataAdmin));
        //$this->assign('dataAdmin', $dataAdmin);
        $this->assign('list', [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'About', 'url' => '/about', 'menu' => [
                ['label' => 'History', 'url' => '/about/history'],
                ['label' => 'Vision', 'url' => '/about/vision', 'menu' => [
                    ['label' => 'Submenu 1', 'url' => '/about/vision/submenu1'],
                    ['label' => 'Submenu 2', 'url' => '/about/vision/submenu2'],
                ]],
                ['label' => 'Mission', 'url' => '/about/mission'],
            ]],
            ['label' => 'Service', 'url' => '/service'],
            ['label' => 'Product', 'url' => '/product'],
            ['label' => 'Contact', 'url' => '/contact'],
        ]);
    }
}