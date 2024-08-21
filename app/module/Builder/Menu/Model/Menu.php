<?php declare(strict_types=1);

namespace Builder\Menu\Model;

use Framework\Database\Abstract\Model;

/**
 * @class Builder\Menu\Model\Menu
 */
class Menu extends Model
{
    /**
     * @var array $listing
     */
    private array $listing = [];

    /**
     * @return array $listing
     */
    public function getListing(): array
    {
        return $this->listing;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setListing(array $data): static
    {
        $this->listing = $data;

        return $this;
    }
}