<?php declare(strict_types=1);

namespace Framework\Dom\Node;

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
 * @class Framework\Dom\Node\Text
 * @package Framework\Dom\Node
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class Text
{
    /**
     * @param string $text
     */
    public function __construct(
        private string $text
    ) {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return trim($this->text);
    }
}