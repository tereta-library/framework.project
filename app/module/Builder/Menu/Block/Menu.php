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
                    ['label' => 'Home', 'link' => '/', 'identifier' => 'home'],
                    ['label' => 'About', 'link' => '/about'],
                    ['label' => 'Contact', 'link' => '/contact']
                ]),
            ]);
        }

        $list = MenuConverter::toArray($menuModel->getListing());

        $this->assign('dataAdmin', "@dataAdmin " . json_encode(['menu' => 'default']));
        $this->assign('list', $list);
    }
}