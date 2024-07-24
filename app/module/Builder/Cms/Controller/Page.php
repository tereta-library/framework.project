<?php declare(strict_types=1);

namespace Builder\Cms\Controller;

use Framework\Http\Interface\Controller;

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
 * @class Builder\Cms\Controller\Page
 * @package Builder\Cms\Controller
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class Page implements Controller
{
    /**
     * @router expression GET /^\/cms\/page\/(.*)$/Usi
     * @param string $identifier
     * @return string
     */
    public function render(string $identifier): string
    {
        return json_encode(func_get_args());
    }
}