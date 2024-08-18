<?php declare(strict_types=1);

namespace Builder\Site\Setup;

use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;
use Framework\Database\Value\Now as ValueNow;
use Exception;

/**
 * ···························WWW.TERETA.DEV······························
 * ·······································································
 * : _____                        _                     _                :
 * :|_   _|   ___   _ __    ___  | |_    __ _        __| |   ___  __   __:
 * :  | |    / _ \ | '__|  / _ \ | __|  / _` |      / _` |  / _ \ \ \ / /:
 * :  | |   |  __/ | |    |  __/ | |_  | (_| |  _  | (_| | |  __/  \ V / :
 * :  |_|    \___| |_|     \___|  \__|  \__,_| (_)  \__,_|  \___|   \_/  :
 * ·······································································
 * ·······································································
 *
 * @class Builder\Site\Setup\Structure
 * @package Builder\Site\Setup
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Structure extends Upgrade
{
    /**
     * @date 2024-08-10 00:00:00 Created
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $connection = $this->connection;
        // Create site
        $tableQuery = Factory::createTable('site');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Site ID');
        $tableQuery->addString('identifier')->setNotNull()->setUnique()->setComment('Site identifier');

        $tableQuery->addString('name', 64)->setComment('Site name');
        $tableQuery->addString('tagline', 64)->setComment('Site tagline');
        $tableQuery->addString('logoImage', 254)->setComment('Site logo image');
        $tableQuery->addString('iconImage', 254)->setComment('Site icon image');
        $tableQuery->addString('phone', 64)->setComment('Site phone');
        $tableQuery->addString('email', 64)->setComment('Site email');
        $tableQuery->addString('address', 127)->setComment('Site address');
        $tableQuery->addString('copyright', 127)->setComment('Site copyright');

        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow())->setComment('Site created at');
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow())->setComment('Site updated at');
        $connection->query($tableQuery->build());

        // Create site configuration
        $tableQuery = Factory::createTable('siteConfiguration');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Configuration ID');
        $tableQuery->addString('path', $tableQuery::TYPE_VARCHAR)->setNotNull()->setUnique()->setComment('Configuration path');
        $connection->query($tableQuery->build());

        // Create site configuration value
        $tableQuery = Factory::createTable('siteConfigurationValue');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Configuration value ID');
        $tableQuery->addForeign($connection, 'pathId')->foreign('siteConfiguration', 'id')->setComment('Configuration path ID');
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id')->setComment('Site ID');
        $tableQuery->addString('value', $tableQuery::TYPE_VARCHAR)->setDefault(null)->setComment('Configuration value');
        $tableQuery->addUnique('pathId', 'siteId');
        $connection->query($tableQuery->build());

        // Create site domain
        $tableQuery = Factory::createTable('siteDomain');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('Domain ID');
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id')->setComment('Site ID');
        $tableQuery->addString('domain', $tableQuery::TYPE_VARCHAR)->setUnique()->setComment('Domain name');
        $tableQuery->addBoolean('secure')->setDefault(0)->setComment('Domain secure');
        $tableQuery->addInteger('primaryDomain', 1)->setComment('Primary domain');
        $connection->query($tableQuery->build());

        // Create site user
        $tableQuery = Factory::createTable('siteUser');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey();
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id')->setComment('Site ID');
        $tableQuery->addForeign($connection, 'userId')->foreign('user', 'id')->setComment('User ID');
        $tableQuery->addInteger('acl', 1, false)->setNotNull()->setDefault(0)->setComment('User ACL');
        $tableQuery->addUnique('siteId', 'userId');
        $connection->query($tableQuery->build());
    }
}