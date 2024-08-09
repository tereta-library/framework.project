<?php declare(strict_types=1);

namespace Framework\Database;

use Framework\Database\Select\Builder as SelectBuilder;
use Framework\Database\Select\Factory as SelectFactory;
use Framework\Database\Table\Describe\Builder as DescribeBuilder;

/**
 * Class Factory
 * @class Framework\Database\Factory
 * @package Framework\Database
 */
class Factory
{
    /**
     * @param array ...$columns
     * @return SelectBuilder
     */
    public static function createSelect(...$columns): SelectBuilder
    {
        return (new SelectFactory)->create($columns);
    }

    public static function createDescribe(?string $table = null): DescribeBuilder
    {
        return new DescribeBuilder($table);
    }
}