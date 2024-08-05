<?php declare(strict_types=1);

namespace Framework\Database\Select;

/**
 * @class Framework\Database\Select\Builder
 */
class Builder
{
    /**
     * @var string|null
     */
    private ?string $table = null;

    /**
     * @var array|string[]
     */
    private array $columns = [];

    /**
     * @var array
     */
    private array $where = [];

    /**
     * @var array
     */
    private array $leftJoin = [];

    /**
     * @param array $columns
     */
    public function __construct(array $columns = ['*'])
    {
        $this->columns = $columns;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns = ['*']): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    private int $valueCounter = 0;

    private array $params = [];

    /**
     * @param string $condition
     * @param ...$variables
     * @return $this
     */
    public function where(string $condition, ...$variables): static
    {
        foreach ($variables as $key => $variable) {
            $field = $this->valueCounter ? ":field{$this->valueCounter}" : ":field";
            $this->params[$field] = $variable;
            $condition = str_replace('?', $field, $condition);
        }

        $this->where[] = [
            'operator' => 'AND',
            'condition' => $condition
        ];

        $this->valueCounter++;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;

        $sql .= $this->buildWhere();

        return $sql;
    }

    /**
     * @return string
     */
    private function buildWhere(): string
    {
        $sql = '';
        if (empty($this->where)) {
            return '';
        }

        $where = '';
        foreach ($this->where as $item) {
            if ($where) {
                $where .= " {$item['operator']} ";
            }
            $where .= $item['condition'];
        }

        $sql .= " WHERE {$where}";

        return $sql;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}