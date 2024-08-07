<?php declare(strict_types=1);

namespace Framework\Database\Abstract\Resource;

use Framework\Database\Abstract\Model as ItemModel;
use Framework\Database\Factory;
use Framework\Database\Singleton as SingletonDatabase;
use PDO;

/**
 * @class Framework\Database\Abstract\Resource\Model
 */
abstract class Model
{
    private ?array $description = null;

    public function __construct(private string $table, private ?string $idField = null)
    {
    }

    private function prepareModel(): void
    {
        if ($this->description) return;

        $pdoStatement = SingletonDatabase::getConnection()->prepare(
            Factory::createDescribe($this->table)->build()
        );
        $pdoStatement->execute();

        $this->description = [];
        foreach ($pdoStatement->fetchAll(PDO::FETCH_ASSOC) as $column) {
            $this->description[$column['Field']] = $column;
            if ($column['Key'] === 'PRI') {
                $this->idField = $column['Field'];
            }
        }
    }

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

    public function save(ThinModel $model): void
    {
        $data = $model->getData();
    }
}