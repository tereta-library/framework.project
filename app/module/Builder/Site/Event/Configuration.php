<?php declare(strict_types=1);

namespace Builder\Site\Event;

use Builder\Site\Model\Repository as SiteRepository;
use Exception;

/**
 * @class Builder\Site\Event\Configuration
 */
class Configuration
{
    /**
     * @param array $arguments
     * @return void
     * @throws Exception
     */
    public function viewConfig(array $arguments): void
    {
        $applicationManager = $arguments['manager'];
        if (!isset($_SERVER['HTTP_HOST'])) {
            return;
        }

        $siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST']);
        $theme = $siteModel->getConfig()->get('view.template');
        if (!$theme) {
            return;
        }

        $applicationConfig = $applicationManager->getConfig();
        $viewDirectory = $applicationConfig->get('viewDirectory');
        $generatedDirectory = $applicationConfig->get('generatedDirectory');
        $applicationConfig->set('themeDirectory', "{$viewDirectory}/{$theme}");
        $applicationConfig->set('generatedThemeDirectory', "{$generatedDirectory}/{$theme}");
    }
}