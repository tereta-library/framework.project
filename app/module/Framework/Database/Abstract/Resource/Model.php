<?php declare(strict_types=1);

namespace Framework\Database\Abstract\Resource;

use Framework\Database\Abstract\Model as ItemModel;
use Framework\Database\Factory;
use Framework\Database\Singleton as SingletonDatabase;
use PDO;
use Exception;
use Framework\Database\Facade;

/**
 * @class Framework\Database\Abstract\Resource\Model
 */
abstract class Model
{
    /**
     * @var array|null
     */
    private ?array $description = null;

    private PDO $connection;

    /**
     * @param string $table
     * @param string|null $idField
     * @param string $connectionName
     * @throws Exception
     */
    public function __construct(private string $table, private ?string $idField = null, string $connectionName = 'default')
    {
        $this->connection = SingletonDatabase::getConnection($connectionName);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function prepareModel(): void
    {
        if ($this->description) return;

        $this->description = [];
        foreach (Facade::describeTable(SingletonDatabase::getConnection(), $this->table) as $column) {
            $this->description[$column['Field']] = $column;
            if ($column['Key'] === 'PRI') {
                $this->idField = $column['Field'];
            }
        }
    }

    /**
     * @param ItemModel $model
     * @param string|int|float|null $value
     * @param string|null $field
     * @return void
     * @throws Exception
     */
    public function load(ItemModel $model, string|int|float|null $value = null, ?string $field = null): void
    {
        if (!$field) {
            $this->prepareModel();
            $field = $this->idField;
        }
        if ($value === null) $value = $model->get($field);

        $select = Factory::createSelect('*');
        $select->from($this->table);
        $select->where($field . ' = ?', $value);

        $pdo = SingletonDatabase::getConnection();
        $pdoStatement = $pdo->prepare($select->build());
        foreach ($select->getParams() as $key => $param) {
            $pdoStatement->bindParam($key, $param);
        }
        $pdoStatement->execute();

        $itemData = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $model->setData($itemData);
    }

    /**
     * @param ItemModel $model
     * @return void
     * @throws Exception
     */
    public function save(ItemModel $model): void
    {
        $query = Factory::createInsert($this->table)->values($model->getData());
        $query->updateOnDupilicate();

        $pdoStat = $this->connection->prepare($query->build());
        $result = $pdoStat->execute($query->getParams());
        $e=0;
    }
}