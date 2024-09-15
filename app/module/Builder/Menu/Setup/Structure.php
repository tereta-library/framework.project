<?php declare(strict_types=1);

namespace Builder\Menu\Setup;

use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;
use Framework\Database\Value\Now as ValueNow;
use Exception;

class Structure extends Upgrade
{
    /**
     * @date 2024-08-25 14:00:00 Created
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $connection = $this->connection;

        $tableQuery = Factory::createTable('menu');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Menu ID');
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id')->setComment('Site ID');
        $tableQuery->addString('identifier')->setNotNull()->setComment('Menu identifier');
        $tableQuery->addString('label')->setNotNull()->setDefault('Menu Label')->setComment('Menu identifier');
        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow())->setComment('Site created at');
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow())->setComment('Site updated at');
        $tableQuery->addUnique('siteId', 'identifier');
        $connection->query($tableQuery->build());

        $tableQuery = Factory::createTable('menuItem');
        $tableQuery->addInteger('id')->setAutoIncrement()->setPrimaryKey()->setComment('Item ID');
        $tableQuery->addForeign($connection, 'menuId')->foreign('menu', 'id')->setComment('Menu ID');
        $tableQuery->addForeign($connection, 'parentId')->foreign('menuItem', 'id')->setComment('Parent ID');
        $tableQuery->addForeign($connection, 'typeId')->foreign('pageType', 'id')->setComment('Page type ID');
        $tableQuery->addString('identifier')->setComment('Item identifier, value can be in template "$string#$integer" for example "page#1" or null if not bounded link');
        $tableQuery->addString('label')->setNotNull()->setComment('Item label');
        $tableQuery->addString('link')->setNotNull()->setComment('Item link');
        $tableQuery->addIndex('identifier');
        $connection->query($tableQuery->build());
    }
}