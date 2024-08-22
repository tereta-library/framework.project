<?php declare(strict_types=1);

namespace Builder\Cms\Controller;

use Framework\Http\Interface\Controller;
use Framework\Application\Manager;
use Exception;
use Framework\User\Model\Token as ModelToken;
use Framework\User\Model\Resource\Token as ResourceModelToken;

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
 * @class Builder\Cms\Controller\View
 * @package Builder\Cms\Controller
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
     * @router expression GET /^\/cms\/page\/(\w*)$/Usi
     * @param string $token
     * @return string
     * @throws Exception
     */
    public function render(string $token): string
    {
        $config = Manager::getInstance()->getConfig();
        $view = Manager::getInstance()->getView();

        $view->initialize('cms')
            ->getBlockById('content')
            ->assign('content', 'Test content');
        return $view->render();
    }
}