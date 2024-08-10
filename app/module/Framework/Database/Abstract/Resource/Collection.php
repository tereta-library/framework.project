<?php declare(strict_types=1);

namespace Framework\Database\Abstract\Resource;

use Framework\Database\Abstract\Resource\Model as ResourceModel;
use Iterator;
use Framework\Database\Select\Factory as SelectFactory;
use Framework\Database\Singleton as SingletonConnection;
use Exception;
use PDO;

/**
 * @class Framework\Database\Abstract\Resource\Collection
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

    public function getSelect(bool $reset = false)
    {
        if ($this->select && !$reset) {
            return $this->select;
        }
        $this->select = SelectFactory::create()->from($this->resourceModel->getTable());
        return $this->select;
    }

    private function load(bool $reset = false)
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

    public function rewind() {
        $this->load(true);
    }

    // Возвращаем текущий элемент
    public function current() {
        $this->position++;
        $data = $this->loadStatement->fetch(PDO::FETCH_ASSOC);
        $model = $this->model;
        return new $model($data);
    }

    public function key() {
        return $this->position;
    }

    public function next() {
    }

    public function valid(): bool {
        $this->load();

        return $this->position < $this->count;
    }
}