<?php declare(strict_types=1);

namespace Builder\Content\Model;

use Builder\Content\Model\Resource\Content\Collection;
use Exception;
use Builder\Page\Model\Interface\Url as InterfaceUrl;

/**
 * @class Builder\Content\Model\Url
 */
class Url implements InterfaceUrl
{
    const ?string IDENTIFIER = 'content';

    /**
     * @param array $identifiers
     * @return array
     * @throws Exception
     */
    public function getCollectionByIdentifiers(array $identifiers): array
    {
        $collection = new Collection;
        $identifiers = array_map('intval', $identifiers);
        $identifiers = array_filter($identifiers, 'is_int');
        $collection->getSelect()->where('id IN (' . implode(", ",  $identifiers) . ')');

        $result = [];
        foreach ($collection as $item) {
            $result[$item->get('id')] = [
                'id' => $item->get('id'),
                'title' => $item->get('header'),
            ];
        }

        return $result;
    }
}