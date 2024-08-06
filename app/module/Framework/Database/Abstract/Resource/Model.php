<?php declare(strict_types=1);

namespace Framework\Database\Abstract\Resource;

use Framework\Database\Abstract\Model as ItemModel;
use Framework\Database\Factory;
use Framework\Database\Singleton as SingletonDatabase;

/**
 * @class Framework\Database\Abstract\Resource\Model
 */
abstract class Model
{
    public function __construct(private string $table, private ?string $idField = null)
    {
    }

    public function load(ItemModel $model): void
    {
        $select = Factory::createSelect('*');
        $select->from($this->table);
        $select->where($this->idField . ' = ?', $model->get('id'));

        $select = Factory::createSelect('*');
        $select->from($this->table);
        $select->where($this->idField . ' = ?', $model->get('id'));

        $pdo = SingletonDatabase::getConnection();
        $pdoStatement = $pdo->prepare($select->build(), $select->getParams());
        $pdoStatement->execute($select->getParams());
        $itemData = $pdoStatement->fetch();
        var_dump($itemData);
        echo $select;
        $data = [];
        $model->setData($data);
    }

    public function save(ThinModel $model): void
    {
        $data = $model->getData();
    }
}