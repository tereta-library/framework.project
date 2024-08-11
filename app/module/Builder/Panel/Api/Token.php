<?php declare(strict_types=1);

namespace Builder\Panel\Api;

use Framework\Api\Interface\Api;
use Framework\User\Model\Resource\Token as TokenResourceModel;
use Framework\User\Model\Token as TokenModel;
use Exception;

class Token implements Api
{
    /**
     * @param array $input
     * @return array
     * @throws Exception
     * @api admin/token
     */
    public function getToken(array $input): array
    {
        (new TokenResourceModel())->load($userModel = new TokenModel());
        return ['token' => 'asd', 'debug' => $input];
    }
}