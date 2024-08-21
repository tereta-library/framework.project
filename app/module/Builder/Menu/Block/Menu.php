<?php declare(strict_types=1);

namespace Builder\Menu\Block;

use Builder\Menu\Helper\Converter as MenuConverter;
use Framework\View\Php\Template;
use Builder\Menu\Model\Menu\Repository as MenuRepository;
use Exception;
use Builder\Site\Model\Repository as SiteRepository;

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
            $siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST']);
            $menuModel = MenuRepository::getInstance()->getByIdentifier($this->get('identifier'), $siteModel->get('id'));
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

        $this->assign('dataAdmin', "@dataAdmin " . json_encode(['menu' => $this->get('identifier')]));
        $this->assign('list', $list);
    }
}