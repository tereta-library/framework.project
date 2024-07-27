<?php declare(strict_types=1);

namespace Framework\Dom\Interface;

/**
 * Interface Framework\Dom\Interface\Node
 */
interface Node
{
    public function render(): string;

    public function __toString(): string;
}