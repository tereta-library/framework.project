<?php declare(strict_types=1);

namespace Builder\Menu\Block;

use Builder\Menu\Helper\Converter as MenuConverter;
use Builder\Site\Model\Repository as SiteRepository;
use Exception;
use Framework\Repository as MenuRepository;
use Framework\View\Php\Template;

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
                'siteId' => $siteModel->get('id'),
                'identifier' => $this->get('identifier'),
                'label' => $this->get('label'),
                'data' => json_encode([
                    ['label' => 'Home', 'link' => '/', 'identifier' => 'home'],
                    ['label' => 'About', 'link' => '/about'],
                    ['label' => 'Contact', 'link' => '/contact']
                ]),
            ]);
        }

        $list = MenuConverter::toArray($menuModel->getListing());

        $this->assign('list', $list);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        return '<!-- @dataAdmin ' . json_encode(['menu' => ['identifier' => $this->get('identifier'), 'label' => $this->get('label')]]) . ' -->' .
            parent::render();
    }
}