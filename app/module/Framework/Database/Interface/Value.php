<?php declare(strict_types=1);

namespace Framework\Database\Interface;

/**
 * @interface Framework\Database\Interface\Value
 */
interface Value
{
    /**
     * @return string
     */
    public function build(): string;

    /**
     * @return string
     */
    public function __toString(): string;

}