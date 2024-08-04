<?php declare(strict_types=1);

namespace Builder\Panel\Api;

use Framework\Api\Interface\Api;

class Token implements Api
{
    /**
     * @param array $input
     * @return array
     * @api admin/token
     */
    public function getToken(array $input): array
    {
        return ['token' => 'asd', 'debug' => $input];
    }
}