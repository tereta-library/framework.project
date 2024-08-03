<?php declare(strict_types=1);

namespace Builder\Cms\Controller;

use Framework\Http\Interface\Controller;
use Framework\Application\Manager;
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
 * @class Builder\Cms\Controller\View
 * @package Builder\Cms\Controller
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class View implements Controller
{
    /**
     * Sample url: http://127.0.0.1/cms/page/123
     *
     * @router expression GET /^\/cms\/page\/(.*)$/Usi
     * @param string $identifier
     * @return string
     */
    public function render(string $identifier): string
    {
        $config = Manager::instance()->getConfig();
        $view = Manager::instance()->getView();
throw new \Exception('eer');
        try {
            return $view->render('cms');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}