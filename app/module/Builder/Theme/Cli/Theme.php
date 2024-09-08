<?php declare(strict_types=1);

namespace Builder\Theme\Cli;

use Exception;
use Framework\Application\Manager;
use Framework\Cli\Interface\Controller;
use Builder\Theme\Model\Theme as ThemeModel;
use Builder\Theme\Model\Resource\Theme as ThemeResource;
use Builder\Theme\Model\Resource\Theme\Collection as ThemeCollection;
use DirectoryIterator;
use Framework\Cli\Symbol;

/**
 * @class Builder\Theme\Cli\Theme
 */
class Theme implements Controller
{
    private ThemeResource $themeResource;

    private ThemeCollection $themeCollection;

    public function __construct()
    {
        $this->themeResource = new ThemeResource;
        $this->themeCollection = new ThemeCollection;
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
        $directoryIterator = new DirectoryIterator($viewDirectory);
        foreach ($directoryIterator as $item) {
            if($item->isDot()) continue;
            if (str_starts_with($item->getFilename(), '.')) continue;

            $this->registerTheme($viewDirectory, $item);
        }

        foreach ($this->themeCollection as $item) {
            $item->get('identifier');
            $this->unregisterTheme($viewDirectory, $item);
        }

        echo PHP_EOL;
    }

    private function unregisterTheme(string $viewDirectory, ThemeModel $themeModel): void
    {
        $themeDirectory = "{$viewDirectory}/{$themeModel->get('identifier')}";
        $descriptionFile = "{$themeDirectory}/view.json";

        $description = [];
        if (is_file($descriptionFile)) {
            $description = json_decode(file_get_contents($descriptionFile), true);
        }

        if ($this->isValidDescription($description)) {
            return;
        }

        $this->themeResource->delete($themeModel);
        echo Symbol::COLOR_RED . "DELETED: {$themeModel->get('identifier')}\n" . Symbol::COLOR_RESET;
    }

    private function isValidDescription(array $description): bool
    {
        $allowed = isset($description['name']) && $description['name'];
        $allowed = $allowed && isset($description['description']) && $description['description'];
        $allowed = $allowed && isset($description['imageTable']) && $description['imageTable'];
        $allowed = $allowed && isset($description['imageMobile']) && $description['imageMobile'];
        if (!$allowed) return false;
        return true;
    }

    private function registerTheme(string $viewDirectory, DirectoryIterator $item): void
    {
        $themeDirectory = "{$viewDirectory}/{$item->getFilename()}";
        $descriptionFile = "{$themeDirectory}/view.json";

        $description = [];
        if (is_file($descriptionFile)) {
            $description = json_decode(file_get_contents($descriptionFile), true);
        }

        if (!$this->isValidDescription($description)) {
            return;
        }

        $this->themeResource->load($themeModel = new ThemeModel, ['identifier' => $item]);
        if ($themeModel->get('id')) {
            return;
        }

        $themeModel->set('identifier', $item->getFilename());

        $this->themeResource->save($themeModel);

        echo Symbol::COLOR_GREEN . "CREATED: {$description['name']} [{$item->getFilename()}]\n" . Symbol::COLOR_RESET;
    }
}