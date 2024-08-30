<?php declare(strict_types=1);
namespace Builder\Page\Router;

use Builder\Page\Model\Interface\Url as InterfaceUrl;
use Framework\Http\Interface\Router as RouterInterface;
use Framework\Http\Router\Action;
use Builder\Page\Model\Resource\Url as UrlResource;
use Builder\Page\Model\Url\Repository as UrlRepository;
use Exception;
use Builder\Page\Model\Type\Repository as UrlTypeRepository;

/**
 * @class Builder\Page\Router\Url
 */
class Url implements RouterInterface
{
    const ROUTER = "url";

    /**
     * @var array $routes
     */
    private static array $routes = [];

    /**
     * @var bool
     */
    private static bool $enabled = true;

    /**
     * @var UrlResource $urlResource
     */
    private UrlResource $urlResource;

    /**
     * @param string $action
     * @param array $params
     */
    public function __construct(private string $action, array $params)
    {
        $this->urlResource = new UrlResource();

        static::$routes[] = [
            'action' => $action,
            'params' => $params,
        ];
    }

    /**
     * @param string $method
     * @param string $host
     * @param string $path
     * @return Action|null
     * @throws Exception
     */
    public function run(string $method, string $host, string $path): ?Action
    {
        if (!static::$enabled) {
            return null;
        }

        static::$enabled = false;
        try {
            $urlModel = UrlRepository::getInstance()->getByUrl($host, $path);
        } catch (Exception $e) {
            return null;
        }
        $typeModel = UrlTypeRepository::getInstance()->getTypeById($urlModel->get('typeId'));
        $typeClass = $typeModel->get('identifier');
        $typeProcessor = new $typeClass;
        if ($typeProcessor instanceof InterfaceUrl === false) {
            throw new Exception('Type collection must implement ' . InterfaceUrl::class);
        }
        $shortIdentifier = $typeProcessor::IDENTIFIER;
        if (!$shortIdentifier) {
            throw new Exception('Type collection must have IDENTIFIER constant to identify the controller for the URL');
        }


        foreach (static::$routes as $route) {
            list ($routeMethod, $routeType) = $route['params'];
            if ($routeMethod != 'ANY' && $method != $routeMethod) {
                continue;
            }

            if ($shortIdentifier != $routeType) {
                continue;
            }

            return new Action($route['action'], [$urlModel->get('identifier')]);
        }

        return null;
    }
}