<?php declare(strict_types=1);

namespace Builder\Content\Controller;

use Builder\Content\Model\Content as ModelContent;
use Builder\Content\Model\Resource\Content as ResourceContent;
use Builder\Site\Model\Repository as SiteRepository;
use Framework\Application\Controller\Error as ErrorController;
use Framework\Http\Interface\Controller;
use Exception;
use ScssPhp\ScssPhp\Compiler as ScssCompiler;

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
 * @class Builder\Content\Controller\Css
 * @package Builder\Content\Controller
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Css implements Controller
{
    /**
     * @router expression GET /^\/css\/content\/([a-z0-9]+)\.css$/Usi
     * @param string $identifier
     * @return string
     * @throws Exception
     */
    public function render(string $identifier): string
    {
        $siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST']);
        ResourceContent::getInstance()->load($contentModel = new ModelContent, [
            'identifier' => $identifier,
            'siteId' => $siteModel->get('id')
        ]);

        if (!$contentModel->get('id')) {
            return (new ErrorController)->notFound();
        }


        $scss = new ScssCompiler();
        $compilationResult = $scss->compileString($contentModel->get('css'));

        header('Content-Type: text/css');
        return $compilationResult->getCss();
    }
}