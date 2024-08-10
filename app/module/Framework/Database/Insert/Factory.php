<?php declare(strict_types=1);

namespace Framework\Database\Insert;

use Framework\Database\Insert\Builder;

class Factory
{
    public static function create(string $table): Builder
    {
        return new Builder($table);
    }
}