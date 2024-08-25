<?php declare(strict_types=1);

namespace Builder\Page\Api;

use Framework\Api\Interface\Api;

/**
 * @class Builder\Page\Api\Listing
 * @package Builder\Page
 */
class Listing implements Api
{
    /**
     * @return array
     * @api POST /^page\/getListing$/Usi
     */
    public function getListing(): array
    {
        return [];
    }

    /**
     * @return array
     * @api GET /^page\/create/Usi
     */
    public function create(): array
    {
        return [];
    }
}