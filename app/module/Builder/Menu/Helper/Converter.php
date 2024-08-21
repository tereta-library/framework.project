<?php declare(strict_types=1);

namespace Builder\Menu\Helper;

use Builder\Menu\Model\Menu\Item as MenuItemModel;

/**
 * @class Builder\Menu\Helper\Converter
 */
class Converter
{
    /**
     * @param array $data
     * @return array
     */
    public static function toArray(array $data): array
    {
        $result = [];
        foreach ($data as $object) {
            $item = $object->getData();
            if ($object->getMenu()) {
                $item['menu'] = self::toArray($object->getMenu());
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param array $data
     * @param MenuItemModel|null $parentObject
     * @return array
     */
    public static function toObject(array $data, ?MenuItemModel $parentObject = null): array
    {
        $result = [];
        foreach ($data as $value) {
            $menu = [];
            $currentObject = new MenuItemModel([
                'id'      => $value['id'] ?? null,
                'label'   => $value['label'],
                'link'     => $value['link']
            ]);

            if (isset($value['menu']) && $value['menu']) {
                $menu = self::toObject($value['menu'], $currentObject);
            }

            $result[] = $currentObject->setMenu($menu)->setParent($parentObject);
        }

        return $result;
    }

    /**
     * @param array $data
     * @param array $root
     * @return array
     */
    public static function fetchAll(array $data,  array $root = []): array
    {
        $result = [];
        foreach ($data as $object) {
            $root[] = $object;

            if ($object->getMenu()) {
                $root = static::fetchAll($object->getMenu(), $root);
            }
        }

        return $root;
    }
}