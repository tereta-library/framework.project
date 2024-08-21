<?php declare(strict_types=1);

namespace Builder\Menu\Model\Resource\Menu;

use Framework\Database\Abstract\Resource\Model;

/**
 * @class Builder\Menu\Model\Resource\Menu\Item
 */
class Item extends Model
{
    public function __construct()
    {
        parent::__construct('menuItem');
    }
}