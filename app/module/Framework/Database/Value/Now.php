<?php declare(strict_types=1);

namespace Framework\Database\Value;

use Framework\Database\Interface\Value;

/**
 * @class Framework\Database\Value\Now
 */
class Now implements Value
{
    public function build(): string
    {
        return 'NOW()';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}