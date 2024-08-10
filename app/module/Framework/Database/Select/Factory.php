<?php declare(strict_types=1);

namespace Framework\Database\Select;

use Framework\Database\Select\Builder;

/**
 * @class Framework\Database\Select\Factory
 */
class Factory
{
    public static function create(array $columns = ['*']): Builder
    {
        return new Builder($columns);
    }
}