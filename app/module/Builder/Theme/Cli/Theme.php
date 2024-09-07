<?php declare(strict_types=1);

namespace Builder\Theme\Cli;

use Exception;
use Framework\Cli\Interface\Controller;

/**
 * @class Builder\Theme\Cli\Theme
 */
class Theme implements Controller
{
    /**
     * @cli site:theme:register
     * @cliDescription Update registration of themes
     * @return void
     * @throws Exception
     */
    public function registerThemes(): void
    {
        echo 'Registering themes...' . PHP_EOL;
    }
}