<?php declare(strict_types=1);

namespace Builder\Site\Block;

use Builder\Site\Model\Site as SiteModel;
use Framework\View\Html as Layout;
use Framework\View\Php\Template;
use Builder\Site\Model\Repository as SiteRepository;
use Exception;

/**
 * @class Builder\Site\Block\Data
 */
class Styles extends Template
{
    private array $styles = [];

    public function addStyle(string $style): void
    {
        $this->styles[] = $style;
    }

    public function render(): string
    {
        $this->assign('styles', $this->styles);

        return parent::render();
    }
}