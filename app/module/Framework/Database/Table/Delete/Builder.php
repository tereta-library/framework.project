<?php declare(strict_types=1);

namespace Framework\Database\Table\Delete;

/**
 * Class Builder
 * @class Framework\Database\Table\Builder
 * @package Framework\Database\Table
 */
class Builder
{
    /**
     * @param string $table
     */
    public function __construct(private string $table)
    {
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return "DELETE FROM {$this->table}";
    }
}