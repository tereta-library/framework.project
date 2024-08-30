<?php declare(strict_types=1);

namespace Builder\Content\Controller;

use Framework\Http\Interface\Controller;
use Framework\Application\Manager;
use Builder\Content\Model\Resource\Content as ResourceContent;
use Builder\Content\Model\Content as ModelContent;
use Exception;

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
 * @class Builder\Content\Controller\View
 * @package Builder\Content\Controller
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class View implements Controller
{
    /**
     * Sample url: http://127.0.0.1/cms/page/123
     *
     * @router url GET content
     * @param string $id
     * @return string
     * @throws Exception
     */
    public function render(string $id): string
    {
        ResourceContent::getInstance()->load($contentModel = new ModelContent, $id);

        $view = Manager::getInstance()->getView();

        $layout = $view->initialize('cms');
        $layout->getBlockById('headSeo')
            ->assign('title', $contentModel->get('seoTitle'))
            ->assign('description', $contentModel->get('description'));

        $layout->getBlockById('content')->setModel($contentModel);

        return $view->render();
    }
}