<?php declare(strict_types=1);

namespace Framework\Dom\Abstract;

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
 * @class Framework\Dom\Abstract\Node
 * @package Framework\Dom\Abstract
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
abstract class Node
{
    /**
     * @var int $positionStart
     */
    private int $positionStart;

    /**
     * @var int $positionEnd
     */
    private int $positionEnd;

    /**
     * @param int $start
     * @param int $end
     * @return $this
     */
    public function setPosition(int $start, int $end): static
    {
        $this->positionStart = $start;
        $this->positionEnd = $end;
        return $this;
    }

    /**
     * @return int
     */
    public function getPositionStart(): int
    {
        return $this->positionStart;
    }
}
