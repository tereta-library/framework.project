<?php declare(strict_types=1);

namespace Builder\Menu\Model\Menu;

use Framework\Database\Abstract\Model;

/**
 * @class Builder\Menu\Model\Menu\Item
 */
class Item extends Model
{
    /**
     * @var array $menu
     */
    private array $menu = [];

    /**
     * @var Item|null $parent
     */
    private ?Item $parent = null;

    /**
     * @param array $data
     * @return $this
     */
    public function setMenu(array $data): static
    {
        $this->menu = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * @param Item|null $parent
     * @return $this
     */
    public function setParent(?Item $parent = null): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Item|null
     */
    public function getParent(): ?Item
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $result = parent::getData();
        $result['menu'] = $this->getMenu();
        return $result;
    }
}