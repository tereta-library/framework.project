<?php declare(strict_types=1);

namespace Builder\Theme\Setup;

use Exception;
use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;

/**
 * @class Builder\Theme\Setup\Themes
 */
class Themes extends Upgrade
{
    /**
     * @date 2024-09-07 00:00:00 Created
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $connection = $this->connection;

        $tableQuery = Factory::createTable('theme');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Theme ID');
        $tableQuery->addString('identifier', $tableQuery::TYPE_VARCHAR)->setUnique()->setComment('Layout directory name');
        $connection->query($tableQuery->build());
    }
}