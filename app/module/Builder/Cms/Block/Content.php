<?php declare(strict_types=1);

namespace Builder\Cms\Block;

use Framework\View\Php\Template;

class Content extends Template
{
    public function render(): string
    {
        $this->assign('content', 'Hello World!');

        return parent::render();
    }
}