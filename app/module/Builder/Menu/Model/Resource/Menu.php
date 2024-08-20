<?php declare(strict_types=1);

namespace Builder\Menu\Model\Resource;

use Framework\Database\Abstract\Resource\Model;

/**
 * @class Builder\Menu\Model\Resource\Menu
 */
class Menu extends Model
{
    public function __construct()
    {
        parent::__construct('menu');
    }
}