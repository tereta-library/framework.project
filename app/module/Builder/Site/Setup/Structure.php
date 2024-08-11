<?php declare(strict_types=1);

namespace Builder\Site\Setup;

use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;
use Framework\Database\Value\Now as ValueNow;
use PDO;
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
     * @setupTime 2024-08-10 00:00:00
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $connection = $this->connection;
        $tableQuery = Factory::createTable('site');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey();
        $tableQuery->addString('identifier')->setNotNull()->setUnique();

        $tableQuery->addString('name', 64);
        $tableQuery->addString('tagline', 64);
        $tableQuery->addString('logoImage', 254);
        $tableQuery->addString('iconImage', 254);
        $tableQuery->addString('phone', 64);
        $tableQuery->addString('email', 64);
        $tableQuery->addString('address', 127);
        $tableQuery->addString('copyright', 127);
        $tableQuery->addString('additionalData', $tableQuery::TYPE_TEXT);

        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow());
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow());
        $connection->query($tableQuery->build());
    }

    /**
     * @setupTime 2024-08-11 00:00:00
     * @return void
     * @throws Exception
     */
    public function createConfiguration(): void
    {
        $connection = $this->connection;

        $tableQuery = Factory::createTable('siteConfiguration');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey();
        $tableQuery->addForeign($connection, 'siteId')->foreign('site', 'id');
        $tableQuery->addString('path', 64);
        $tableQuery->addString('value', 254);
        $tableQuery->addUnique('siteId', 'path');
        $connection->query($tableQuery->build());
    }
}