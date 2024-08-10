<?php declare(strict_types=1);

namespace Framework\Database;

use Framework\Database\Select\Builder as SelectBuilder;
use Framework\Database\Select\Factory as SelectFactory;
use Framework\Database\Create\Builder as CreateBuilder;
use Framework\Database\Create\Factory as CreateFactory;
use Framework\Database\Insert\Builder as InsertBuilder;
use Framework\Database\Insert\Factory as InsertFactory;

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
        return SelectFactory::create($columns);
    }

    /**
     * @param string $table
     * @return CreateBuilder
     */
    public static function createTable(string $table): CreateBuilder
    {
        return CreateFactory::create($table);
    }

    /**
     * @param string $table
     * @return InsertBuilder
     */
    public static function createInsert(string $table): InsertBuilder
    {
        return InsertFactory::create($table);
    }
}