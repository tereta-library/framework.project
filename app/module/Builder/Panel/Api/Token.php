<?php declare(strict_types=1);

namespace Builder\Panel\Api;

use Framework\Api\Interface\Api;

class Token implements Api
{
    /**
     * @return string|array|int
     * @api admin/token
     */
    public function getToken(): string|array|int
    {
        return ['token' => 'asd'];
    }
}