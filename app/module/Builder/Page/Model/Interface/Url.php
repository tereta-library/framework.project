<?php declare(strict_types=1);

namespace Builder\Page\Model\Interface;

/**
 * @interface Builder\Page\Model\Interface\Url
 */
interface Url
{
    /**
     * Is used to identify the controller of the URL, it is used in the Builder\Page\Api\Listing and Builder\Page\Router\Url
     *
     * @var string|null IDENTIFIER
     */
    const ?string IDENTIFIER = null;

    /**
     * Create collection by identifiers, it is used in the Builder\Page\Api\Listing
     *
     * @param array $identifiers
     * @return array
     */
    public function getCollectionByIdentifiers(array $identifiers): array;
}