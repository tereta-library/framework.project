<?php declare(strict_types=1);

namespace Builder\Menu\Model\Resource\Menu\Item;

use Framework\Database\Abstract\Resource\Collection as ResourceCollection;
use Builder\Menu\Model\Resource\Menu\Item as ResourceModel;
use Builder\Menu\Model\Menu\Item as Model;

/**
 * @class Builder\Menu\Model\Resource\Menu\Item\Collection
 */
class Collection extends ResourceCollection
{
    public function __construct()
    {
        parent::__construct(ResourceModel::class, Model::class);
    }
}