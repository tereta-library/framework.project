<?php declare(strict_types=1);

namespace Framework\Database\Abstract;

/**
 * @class Framework\Database\Abstract\Model
 * @package Framework\Database\Abstract
 */
abstract class Model
{
    /**
     * @var array
     */
    private array $originalData = [];

    /**
     * @param array $data
     */
    public function __construct(private array $data = [])
    {
        $this->originalData = $data;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, string|int|bool $value): void
    {
        $this->set($key, $value);
    }

    /**
     * @return array
     */
    public function _toArray(): array
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        $this->unset($name);
    }

    /**
     * @param array $data
     * @param bool $asOrigin
     * @return $this
     */
    public function setData(array $data, bool $asOrigin = false): static
    {
        $this->data = $data;

        if ($asOrigin) {
            $this->originalData = $data;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, string|int|bool $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function unset(string $key): static
    {
        unset($this->data[$key]);
        return $this;
    }
}