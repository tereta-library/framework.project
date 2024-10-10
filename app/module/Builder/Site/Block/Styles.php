<?php declare(strict_types=1);

namespace Builder\Site\Block;

use Framework\View\Php\Template;
use Exception;

/**
 * @class Builder\Site\Block\Styles
 */
class Styles extends Template
{
    /**
     * @var array $styles
     */
    private array $styles = [];

    /**
     * @param string $style
     * @return void
     */
    public function addStyle(string $style): void
    {
        $this->styles[] = $style;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        $this->styles[] = "/css/site.css";
        $this->assign('styles', $this->styles);

        return parent::render();
    }
}