<?php declare(strict_types=1);

namespace Framework\User\Setup;

use Framework\Application\Setup\Abstract\Upgrade;
use Framework\Database\Factory;
use Framework\Database\Value\Now as ValueNow;
use PDO;

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
 * @class Framework\User\Setup\Structure
 * @package Framework\User\Setup
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Structure extends Upgrade
{
    /**
     * @setupTime 2024-08-10 21:04:33
     * @param PDO $connection
     * @return void
     */
    public function user(): void
    {
        $connection = $this->connection;
        $tableQuery = Factory::createTable('user');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey();
        $tableQuery->addString('identifier')->setNotNull()->setUnique();
        $tableQuery->addString('password')->setNotNull();
        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow());
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow());
        $connection->query($tableQuery->build());

        $tableQuery = Factory::createTable('token');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey();
        $tableQuery->addForeign($connection, 'userId')->foreign('user', 'id');
        $tableQuery->addString('token')->setNotNull();
        $tableQuery->addString('ip')->setNotNull();
        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow());
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow());
        $connection->query($tableQuery->build());
    }
}