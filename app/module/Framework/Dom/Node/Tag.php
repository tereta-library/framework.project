<?php declare(strict_types=1);

namespace Framework\Dom\Node;

use Framework\Dom\Interface\Node as NodeInterface;
use Framework\Dom\Abstract\Node as NodeAbstract;

/**
 * ···························WWW.TERETA.DEV······························
 * ·······································································
 * : _____                        _                     _                :
 * :|_   _|   ___   _ __    ___  | |_    __ _        __| |   ___  __   __:
 * :  | |    / _ \ | '__|  / _ \ | __|  / _` |      / _` |  / _ \ \ \ / /:
 * :  | |   |  __/ | |    |  __/ | |_  | (_| |  _  | (_| | |  __/  \ V / :
 * :  |_|    \___| |_|     \___|  \__|  \__,_| (_)  \__,_|  \___|   \_/  :
 * ·······································································
 * ·······································································
 *
 * @class Framework\Dom\Node\Tag
 * @package Framework\Dom\Node
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Tag extends NodeAbstract implements NodeInterface
{
    const int TAG_NOTSET = 0;
    const int TAG_OPEN = 1;
    const int TAG_CLOSE = 2;
    const int TAG_SELF_CLOSE = 3;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var int $type
     */
    private int $type = self::TAG_NOTSET;

    /**
     * @var array $attributes
     */
    private array $attributes;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return $this
     */
    public function setAttribute(string $name, ?string $value): static
    {
        if ($value === null) {
            unset($this->attributes[$name]);
            return $this;
        }

        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @return array $attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAttribute(string $name): string
    {
        return $this->attributes[$name] ?? '';
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $tag = $this->name;

        foreach ($this->attributes as $key => $value) {
            $value = str_replace('"', '&quot;', $value);
            $tag .= " " . $key . "=\"" . $value . "\"";
        }

        switch ($this->type) {
            case(self::TAG_OPEN):
                return "<" . $tag . ">";
            case(self::TAG_CLOSE):
                return "</" . $this->name . ">";
            case(self::TAG_SELF_CLOSE):
                return "<" . $tag . "/>";
        }
        return "<" . $this->name . ">";
    }
}