<?php declare(strict_types=1);

namespace Builder\Content\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Exception;
use Framework\Application\Manager\Http\Parameter\Post as PostParameter;

class Media implements Api
{
    use AdministratorTrait;

    /**
     * @param PostParameter $postParameter
     * @return array
     * @api POST /^content\/uploadFile$/Usi
     */
    public function uploadFile(PostParameter $postParameter): array
    {
        return [
            'name' => 'test.png',
            'url' => 'https://example.com/test.png',
            'type' => 'file',
        ];
    }
}
