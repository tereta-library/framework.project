<?php declare(strict_types=1);

namespace Framework\User\Setup;

use Framework\Application\Setup\Interface\Upgrade;
use Framework\Cli\Symbol;

class Structure implements Upgrade
{
    public function setup(): void
    {
        echo Symbol::COLOR_GREEN . "!!!...\n" . Symbol::COLOR_RESET;
    }
}