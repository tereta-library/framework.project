<?php declare(strict_types=1);

namespace Framework\Database\Insert;

use Exception;
use Framework\Database\Interface\Value;

class Builder
{
    /**
     *
     */
    private ?array $fields = null;

    /**
     * @var array
     */
    private array $insertArray = [];

    /**
     * @var array|null
     */
    private ?array $onDuplicate = null;

    private array $values = [];

    /**
     * @param string|null $table
     */
    public function __construct(private ?string $table = null)
    {
    }

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function values(array $data): static
    {
        if ($this->fields === null) {
            $this->fields = array_keys($data);
        } else if ($this->fields != array_keys($data)){
            throw new Exception('Keys on insert values should be same for all insert records');
        }

        $preparedData = [];
        foreach ($this->fields as $key) {
            $preparedData[$key] = $data[$key];
        }

        $this->insertArray[] = $preparedData;

        return $this;
    }

    /**
     * @param ...$fields
     * @return $this
     */
    public function updateOnDupilicate(...$fields): static
    {
        $this->onDuplicate = $fields;
        return $this;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $this->values = [];
        foreach ($this->insertArray as $itemNumber => $itemData) {
            $preparedItem = [];
            foreach ($itemData as $itemKey => $itemValue) {
                if ($itemValue instanceof Value) {
                    $preparedItem[] = $itemValue->build();
                    continue;
                }

                $insertKey = ":{$itemNumber}_{$itemKey}";
                $this->values[$insertKey] = $itemValue;
                $preparedItem[] = $insertKey;
            }
            $values[] = implode(", ", $preparedItem);
        }
        $query = "INSERT INTO {$this->table} (" . implode(", ", $this->fields) . ") VALUES (" . implode('), (', $values) . ")";

        if ($this->onDuplicate) {
            $query .= " ON DUPLICATE KEY UPDATE ";
            $updateArray = [];

            foreach ($this->fields as $field) {
                if (in_array($field, $this->onDuplicate)) continue;
                $updateArray[] = "{$field} = VALUES({$field})";
            }
            $query .= implode(", ", $updateArray);
        }

        return $query;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}