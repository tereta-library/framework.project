<?php declare(strict_types=1);

namespace Framework\Dom\Node;

use Framework\Dom\Interface\Node as NodeInterface;
use Framework\Dom\Abstract\Node as NodeAbstract;

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
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Text extends NodeAbstract implements NodeInterface
{
    /**
     * @param string $text
     */
    public function __construct(
        private string $text
    ) {
    }

    public function render(): string
    {
        return trim($this->text);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}