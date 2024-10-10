<?php declare(strict_types=1);

namespace Builder\Site\Controller;

use Builder\Site\Model\Repository as SiteRepository;
use Framework\Http\Interface\Controller;
use Exception;
use ScssPhp\ScssPhp\Compiler as ScssCompiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Builder\Site\Model\Site\Configuration\Repository as ConfigurationRepository;

/**
 * ···························WWW.TERETA.DEV······························
 * ·······································································
 * : _____                        _                     _                :
 * :|_   _|   ___   _ __    ___  | |_    __ _        __| |   ___  __   __:
 * :  | |    / _ \ | '__|  / _ \ | __|  / _` |      / _` |  / _ \ \ \ / /:
 * :  | |   |  __/ | |    |  __/ | |_  | (_| |  _  | (_| | |  __/  \ V / :
 * :  |_|    \___| |_|     \___|  \__|  \__,_| (_)  \__,_|  \___|   \_/  :
 * ·······································································
 * ·······································································
 *
 * @class Builder\Site\Controller\Css
 * @package Builder\Site\Controller
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Css implements Controller
{
    /**
     * @var ConfigurationRepository $configurationRepository
     */
    private ConfigurationRepository $configurationRepository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST']);
        $this->configurationRepository = ConfigurationRepository::getSiteInstance($siteModel->get('id'));
    }

    /**
     * @router expression GET /^\/css\/site.css$/Usi
     * @return string
     * @throws Exception
     * @throws SassException
     */
    public function render(): string
    {
        $scss = new ScssCompiler();
        $compilationResult = $scss->compileString(
            $this->configurationRepository->get('view.customCss')
        );

        header('Content-Type: text/css');
        return $compilationResult->getCss();
    }
}