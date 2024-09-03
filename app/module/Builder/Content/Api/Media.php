<?php declare(strict_types=1);

namespace Builder\Content\Api;

use Builder\Content\Model\Content as ModelContent;
use Builder\Content\Model\Resource\Content as ResourceContent;
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
        $pageId = $postParameter->get('pageId');
        $fileName = $postParameter->get('fileName');
        $file = $_FILES['file'] ?? null;

        ResourceContent::getInstance()->load($contentModel = new ModelContent, $pageId);
        if (!$contentModel->get('id') || $this->siteModel->get('id') != $contentModel->get('siteId')) {
            throw new Exception('Content not found');
        }

        $filePath = "content/{$contentModel->get('id')}/{$fileName}";
        $fullPath = $this->siteModel->getMedia()->getPath($filePath);
        $fullUrl = $this->siteModel->getMedia()->getUrl($filePath);
        $dirName = dirname($fullPath);
        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true);
        }

        move_uploaded_file($file['tmp_name'], $fullPath);

        list($type) = explode('/', $file['type']);

        return [
            'name' => $fileName,
            'url' => $fullUrl,
            'type' => $type,
        ];
    }
}
