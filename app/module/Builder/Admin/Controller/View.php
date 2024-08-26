<?php declare(strict_types=1);

namespace Builder\Admin\Controller;

use Framework\Application\Manager;
use Framework\Http\Interface\Controller;
use Framework\Application\Manager\Http\Parameter\Get as GetParameter;
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
 * @class Builder\Admin\Controller\View
 * @package Builder\Admin\Controller
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class View implements Controller
{
    /**
     * @router expression GET /^\/admin$/Usi
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        $view = Manager::getInstance()->getView();

        return (string) $view->render('admin');
    }
}