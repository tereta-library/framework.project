<?php declare(strict_types=1);

namespace Framework\Database\Create;

use Framework\Database\Create\Column\Builder as ColumnBuilder;

/**
 * @class Framework\Database\Create\Builder
 * Class Builder
 */
class Builder
{
    /**
     * @var array $columns
     */
    private array $columns = [];

    /**
     * @param string|null $table
     */
    public function __construct(private ?string $table = null)
    {
    }

    /**
     * @param string $table
     * @return $this
     */
    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param string $columnName
     * @param int $length
     * @return ColumnBuilder
     */
    public function addString(string $columnName, int $length = 255): ColumnBuilder
    {
        switch(true) {
            case($length > 4000):
                $column = "{$columnName} LONGTEXT";
                break;
            case($length > 2000):
                $column = "{$columnName} MEDIUMTEXT";
                break;
            case($length > 255):
                $column = "{$columnName} TEXT";
                break;
            default:
                $column = "{$columnName} VARCHAR({$length})";
                break;
        }

        $column = new ColumnBuilder($column, ColumnBuilder::TYPE_TEXT);
        $this->columns[] = $column;
        return $column;
    }

    /**
     * @param string $columnName
     * @param int $length
     * @param int $decimals
     * @return ColumnBuilder
     */
    public function addDecimal(string $columnName, int $length = 10, int $decimals = 2): ColumnBuilder
    {
        $column = "{$columnName} DECIMAL({$length}, {$decimals})";
        $column = new ColumnBuilder($column, ColumnBuilder::TYPE_DECIMAL);
        $this->columns[] = $column;
        return $column;
    }

    /**
     * @param string $columnName
     * @param int $length
     * @param bool $signed
     * @return ColumnBuilder
     */
    public function addInteger(string $columnName, int $length = 4294967295, bool $signed = false): ColumnBuilder
    {
        switch(true) {
            case($signed == true && $length > 2147483647):
                $column = "{$columnName} BIGINT";
                break;
            case($signed == true && $length > 8388607):
                $column = "{$columnName} INT";
                break;
            case($signed == true && $length > 32767):
                $column = "{$columnName} MEDIUMINT";
                break;
            case($signed == true && $length > 127):
                $column = "{$columnName} SMALLINT";
                break;
            case($signed == true):
                $column = "{$columnName} TINYINT";
                break;
            case($length > 4294967295):
                $column = "{$columnName} BIGINT UNSIGNED";
                break;
            case($length > 16777215):
                $column = "{$columnName} INT UNSIGNED";
                break;
            case($length > 65535):
                $column = "{$columnName} MEDIUMINT UNSIGNED";
                break;
            case($length > 255):
                $column = "{$columnName} SMALLINT UNSIGNED";
                break;
            default:
                $column = "{$columnName} TINYINT UNSIGNED";
                break;
        }

        $column = new ColumnBuilder($column, ColumnBuilder::TYPE_INT);
        $this->columns[] = $column;
        return $column;
    }

    /**
     * @return string $this
     */
    public function build(): string
    {
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column->build();
        }

        return "CREATE TABLE {$this->table} (\n  " . implode(",\n  ", $columns) . "\n)";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}