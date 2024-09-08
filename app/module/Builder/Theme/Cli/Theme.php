<?php declare(strict_types=1);

namespace Builder\Theme\Cli;

use Exception;
use Framework\Application\Manager;
use Framework\Cli\Interface\Controller;
use Builder\Theme\Model\Index as ThemeIndex;
use Builder\Theme\Model\Theme\Repository as ThemeRepository;
use Framework\Cli\Symbol;

/**
 * @class Builder\Theme\Cli\Theme
 */
class Theme implements Controller
{
    /**
     * @var ThemeRepository $themeRepository
     */
    private ThemeRepository $themeRepository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->themeRepository = new ThemeRepository;
    }

    /**
     * @cli theme:register
     * @cliDescription Update registration of themes
     * @return void
     * @throws Exception
     */
    public function registerThemes(): void
    {
        $viewDirectory = Manager::getInstance()->getConfig()->get('viewDirectory');
        list($registered, $unregistered) = $this->themeRepository->reindex($viewDirectory);
        foreach ($registered as $item) {
            echo Symbol::COLOR_GREEN . "Registered: {$item}\n" . Symbol::COLOR_RESET;
        }

        foreach ($unregistered as $item) {
            echo Symbol::COLOR_RED . "Unregistered: {$item}\n" . Symbol::COLOR_RESET;
        }
    }
}