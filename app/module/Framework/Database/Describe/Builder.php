<?php declare(strict_types=1);

namespace Framework\Database\Describe;

/**
 * @class Framework\Database\Describe\Builder
 */
class Builder
{
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
     * @return string
     */
    public function build(): string
    {
        return "DESCRIBE {$this->table}";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}