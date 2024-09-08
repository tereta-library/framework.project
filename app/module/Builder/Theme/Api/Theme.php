<?php declare(strict_types=1);

namespace Builder\Theme\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Builder\Theme\Model\Theme as ModelTheme;
use Builder\Theme\Model\Resource\Theme as ResourceTheme;
use Exception;
use Framework\Application\Manager;
use Framework\Application\Manager as ApplicationManager;

/**
 * @class Builder\Theme\Api\Theme
 */
class Theme implements Api
{
    use AdministratorTrait;

    private ResourceTheme $resourceTheme;

    public function __construct() {
        $this->resourceTheme = new ResourceTheme;
    }

    /**
     * @return array
     * @throws Exception
     * @api GET /^site\/configuration\/theme\/current$/Usi
     */
   public function getThemeCurrent(): array
    {
        $siteConfig = $this->siteModel->getConfig();

        try {
            $currentTheme = $siteConfig->get('view.template');
            $this->resourceTheme->load($modelTheme = new ModelTheme, [
                'identifier' => $currentTheme
            ]);
        } catch (Exception $e) {
            throw new Exception('Theme configuration not found');
        }

        if (!$modelTheme->get('id')) {
            throw new Exception('Theme not found');
        }

        return $this->getThemeById((string) $modelTheme->get('id'));
    }

    /**
     * @param string $id
     * @return array
     * @throws Exception
     * @api GET /^site\/configuration\/theme\/((%3C)?-?[0-9]+)$/Usi
     */
    public function getThemeById(string $id): array
    {
        $modelTheme = $this->getThemeModelById($id);
        $urlTheme = "/resource/{$modelTheme->get('identifier')}";

        $viewDirectory = Manager::getInstance()->getConfig()->get('viewDirectory');
        $themeConfigFile = $viewDirectory . '/' . $modelTheme->get('identifier') . '/config.json';
        if (!is_file($themeConfigFile)) {
            throw new Exception('Theme configuration not found');
        }
        $themeConfig = json_decode(file_get_contents($themeConfigFile), true);

        return [
            'id' => $modelTheme->get('id'),
            'name' => 'Theme ' . $modelTheme->get('id'),
            'description' => 'Description of theme ' . $modelTheme->get('id'),
            'price' => 0.00,
            'currency' => 'USD',
            'imageDesktop' => $themeConfig['imageDesktop'] ? "{$urlTheme}/{$themeConfig['imageDesktop']}" : null,
            'imageMobile' => $themeConfig['imageMobile'] ? "{$urlTheme}/{$themeConfig['imageMobile']}" : null
        ];
    }

    private function getThemeModelById(string $id): ModelTheme
    {
        $condition = '>=';
        $direction = $this->resourceTheme::DIRECTION_ASC;
        $forwardSign = '%3C';
        if (str_starts_with($id, $forwardSign)) {
            $condition = '<=';
            $id = substr($id, strlen($forwardSign));
            $direction = $this->resourceTheme::DIRECTION_DESC;
        }

        $this->resourceTheme->where("id {$condition} ?", $id)->order('id', $direction)->load($modelTheme = new ModelTheme);

        if (!$modelTheme->get('id') && $direction === $this->resourceTheme::DIRECTION_ASC) {
            $id = 1;
            $this->resourceTheme->where("id {$condition} ?", $id)->order('id')->load($modelTheme = new ModelTheme);
        } elseif (!$modelTheme->get('id') && $direction === $this->resourceTheme::DIRECTION_DESC) {
            $this->resourceTheme->where("id IS NOT NULL")->order('id', $this->resourceTheme::DIRECTION_DESC)->load($modelTheme = new ModelTheme);
        }

        if (!$modelTheme->get('id')) {
            throw new Exception('Theme not found');
        }

        return $modelTheme;
    }
}