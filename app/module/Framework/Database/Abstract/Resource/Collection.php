<?php declare(strict_types=1);

namespace Framework\Database\Abstract\Resource;

use Framework\Database\Abstract\Resource\Model as ResourceModel;
use Iterator;
use Framework\Database\Select\Factory as SelectFactory;
use Framework\Database\Singleton as SingletonConnection;
use Framework\Database\Abstract\Model;
use Exception;
use PDO;
use PDOStatement;
use Framework\Database\Select\Builder as SelectBuilder;

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
 * @class Framework\Database\Abstract\Resource\Collection
 * @package Framework\Database\Abstract\Resource
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
abstract class Collection implements Iterator
{
    /**
     * @var int
     */
    private int $position = 0;

    /**
     * @var int
     */
    private int $count = 0;

    /**
     * @var ResourceModel
     */
    private ResourceModel $resourceModel;

    private $select;

    private $loadStatement = null;

    private PDO $connection;

    /**
     * @param string $resourceModel
     * @param string $model
     * @param string $connectionName
     * @throws Exception
     */
    public function __construct(string $resourceModel, private string $model, string $connectionName = 'default')
    {
        $this->connection = SingletonConnection::getConnection($connectionName);
        $this->resourceModel = new $resourceModel;
    }

    /**
     * @param bool $reset
     * @return SelectBuilder
     */
    public function getSelect(bool $reset = false): SelectBuilder
    {
        if ($this->select && !$reset) {
            return $this->select;
        }
        $this->select = SelectFactory::create()->from($this->resourceModel->getTable());
        return $this->select;
    }

    /**
     * @param bool $reset
     * @return PDOStatement
     */
    private function load(bool $reset = false): PDOStatement
    {
        if ($this->loadStatement && !$reset) {
            return $this->loadStatement;
        }
        $this->position = 0;
        $query = $this->getSelect();
        $pdoState = $this->connection->query($query->build());
        $pdoState->execute($query->getParams());

        $this->count = $pdoState->rowCount();

        return $this->loadStatement = $pdoState;
    }

    /**
     * @return void
     */
    public function rewind(): void {
        $this->load(true);
    }

    /**
     * @return Model
     */
    public function current(): Model {
        $this->position++;
        $data = $this->loadStatement->fetch(PDO::FETCH_ASSOC);
        $model = $this->model;
        return new $model($data);
    }

    /**
     * @return int
     */
    public function key(): int {
        return $this->position;
    }

    /**
     * @return void
     */
    public function next(): void {
    }

    /**
     * @return bool
     */
    public function valid(): bool {
        $this->load();

        return $this->position < $this->count;
    }
}