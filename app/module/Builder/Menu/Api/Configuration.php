<?php declare(strict_types=1);

namespace Builder\Menu\Api;

use Builder\Menu\Helper\Converter as MenuConverter;
use Builder\Site\Api\Abstract\Admin as AdminAbstract;
use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Exception;
use Framework\Api\Interface\Api;
use Framework\Application\Manager\Http\Parameter\Post as ParameterPost;
use Framework\Repository as MenuRepository;

/**
 * @class Builder\Menu\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    /**
     * @param string $identifier
     * @return array
     * @throws Exception
     * @api GET /^menu\/configuration\/([a-zA-Z0-9_-]+)$/Usi
     */
    public function getConfiguration(string $identifier): array
    {
        $menuModel = MenuRepository::getInstance()->getByIdentifier(
            $identifier,
            $this->siteModel->get('id')
        );

        return MenuConverter::toArray($menuModel->getListing());
    }

    /**
     * @param ParameterPost $payload
     * @return array
     * @api POST /^menu\/configuration$/
     */
    public function setConfiguration(ParameterPost $payload): array
    {
        return [];
    }
}