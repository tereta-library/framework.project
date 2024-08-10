<?php declare(strict_types=1);

namespace Framework\Database\Insert;

use Exception;

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

    public function updateOnDupilicate(array $fields)
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
                $insertKey = ":{$itemNumber}_{$itemKey}";
                $this->values[$insertKey] = $itemValue;
                $preparedItem[] = $insertKey;
            }
            $values[] = implode(", ", $preparedItem);
        }
        $query = "INSERT INTO {$this->table} (" . implode(", ", $this->fields) . ") VALUES (" . implode('), (', $values) . ")";

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