<?php declare(strict_types=1);

namespace Framework\User\Setup;

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
     * @date 2024-01-10 21:04:33 Created
     * @return void
     * @throws Exception
     */
    public function create(): void
    {
        $connection = $this->connection;
        $tableQuery = Factory::createTable('user');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey();
        $tableQuery->addString('identifier')->setNotNull()->setUnique()->setComment('User identifier');
        $tableQuery->addString('password', 64)->setNotNull()->setComment('User password');
        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow())->setComment('User created at');
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow())->setComment('User updated at');
        $connection->query($tableQuery->build());

        $tableQuery = Factory::createTable('userToken');
        $tableQuery->addInteger('id')->setAutoIncrement()->setNotNull()->setPrimaryKey()->setComment('User token ID');
        $tableQuery->addForeign($connection, 'userId')->setComment('Foreign user ID')->foreign('user', 'id');
        $tableQuery->addString('token', 86)->setNotNull()->setComment('User token');
        $tableQuery->addString('ip', 15)->setNotNull()->setComment('User IP');
        $tableQuery->addDateTime('createdAt')->setDefault(new ValueNow())->setComment('User token created at');
        $tableQuery->addDateTime('updatedAt')->setDefault(new ValueNow())->setComment('User token updated at');
        $connection->query($tableQuery->build());
    }
}