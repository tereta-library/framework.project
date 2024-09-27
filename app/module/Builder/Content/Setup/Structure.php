<?php declare(strict_types=1);

namespace Builder\Content\Setup;

use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;
use Exception;
use Builder\Content\Model\Url as UrlTypeModel;

/**
 * @class Builder\Content\Setup\Structure
 */
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

        $tableQuery = Factory::createTable('pageContent');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Page url rewrite ID');
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id')->setComment('Site ID');
        $tableQuery->addString('identifier')->setNotNull()->setComment('Page identifier');
        $tableQuery->addBoolean('status')->setNotNull()->setDefault(0)->setComment('Page status enabled/disabled');
        $tableQuery->addString('seoUri')->setNotNull()->setComment('Page seo URI');
        $tableQuery->addString('seoTitle')->setNotNull()->setComment('Page seo title');
        $tableQuery->addString('header')->setComment('Page header');
        $tableQuery->addString('description')->setComment('Page short description');
        $tableQuery->addString('content', $tableQuery::TYPE_TEXT)->setComment('Page content');
        $tableQuery->addString('css', $tableQuery::TYPE_TEXT)->setComment('Page CSS');
        $tableQuery->addUnique('siteId', 'identifier');
        $tableQuery->addUnique('siteId', 'seoUri');
        $connection->query($tableQuery->build());

        $query = Factory::createInsert('pageType')->values([
            'identifier' => UrlTypeModel::class
        ])->updateOnDupilicate(['identifier']);

        $pdoStat = $connection->prepare($query->build());
        $pdoStat->execute($query->getParams());
    }
}