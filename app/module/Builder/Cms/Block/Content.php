<?php declare(strict_types=1);

namespace Builder\Cms\Block;

use Framework\View\Html\Block\Node;

class Content extends Node
{
    public function render(): string
    {
        $return = parent::render();
        return "<div><div>php</div><div>{$return}</div>";
    }
}