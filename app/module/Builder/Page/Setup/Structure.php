<?php declare(strict_types=1);

namespace Builder\Page\Setup;

use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;
use Exception;

/**
 * @class Builder\Page\Setup\Structure
 */
class Structure extends Upgrade
{
    /**
     * @date 2024-08-25 00:00:00 Created
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $connection = $this->connection;

        $tableQuery = Factory::createTable('pageType');
        $tableQuery->addInteger('id')->setAutoIncrement()->setPrimaryKey()->setComment('Page type ID');
        $tableQuery->addString('identifier')->setUnique()->setComment('Page type identifier');
        $connection->query($tableQuery->build());

        $tableQuery = Factory::createTable('pageUrl');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Page url rewrite ID');
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id')->setComment('Site ID');
        $tableQuery->addForeign($connection, 'typeId')->foreign('pageType', 'id')->setComment('Page type ID');
        $tableQuery->addString('identifier')->setComment('Page type identifier');
        $tableQuery->addString('uri')->setNotNull()->setComment('Page uri');
        $tableQuery->addUnique('siteId', 'uri');
        $connection->query($tableQuery->build());
    }
}