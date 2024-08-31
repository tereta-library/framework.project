<?php declare(strict_types=1);

namespace Builder\Social\Block;

use Framework\View\Php\Template;
use Exception;

/**
 * @class Builder\Social\Block\Icons
 */
class Icons extends Template
{
    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        return '<!-- @dataAdmin ' . json_encode(['social' => []]) . ' -->' .
            parent::render();
    }
}