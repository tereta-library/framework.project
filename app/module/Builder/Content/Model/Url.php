<?php declare(strict_types=1);

namespace Builder\Content\Model;

use Builder\Content\Model\Resource\Content\Collection;
use Framework\Database\Abstract\Resource\Collection as AbstractCollectionModel;

/**
 * @class Builder\Content\Model\Url
 */
class Url
{
    public function getCollectionByIdentifiers(array $identifiers): AbstractCollectionModel
    {
        $collection = new Collection;
        $identifiers = array_filter($identifiers, 'is_int');
        $collection->getSelect()->where('id IN (' . implode(", ",  $identifiers) . ')');

        return $collection;
    }
}