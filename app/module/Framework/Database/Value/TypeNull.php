<?php declare(strict_types=1);

namespace Framework\Database\Value;

use Framework\Database\Interface\Value;

class TypeNull implements Value
{
    public function build(): string
    {
        return 'NULL';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}