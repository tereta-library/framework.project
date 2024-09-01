<?php declare(strict_types=1);

namespace Builder\Social\Block;

use Builder\Site\Model\Repository as SiteRepository;
use Framework\View\Html as Layout;
use Framework\View\Php\Template;
use Exception;
use Builder\Social\Model\Social as SocialModel;
use Builder\Social\Model\Resource\Social as SocialResourceModel;

/**
 * @class Builder\Social\Block\Icons
 */
class Icons extends Template
{
    /**
     * @var SocialResourceModel $socialResourceModel
     */
    private SocialResourceModel $socialResourceModel;

    /**
     * @return void
     */
    protected function construct(): void
    {
        $this->socialResourceModel = new SocialResourceModel;
        parent::construct();
    }

    public function initialize(Layout $layout): void
    {
    }


    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        try {
            $siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST']);
            $this->socialResourceModel->load($socialModel = new SocialModel, $siteModel->get('id'));
        } catch (Exception $e) {
            return 'Icons block exception: ' . $e->getMessage();
        }

        $icons = [];

        $socialItems = [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'youtube' => 'YouTube',
            'pinterest' => 'Pinterest',
        ];

        foreach ($socialItems as $socialKey => $socialItem) {
            if (!$socialModel->get($socialKey)) {
                continue;
            }

            $icons[$socialKey] = [
                'label' => $socialItem,
                'url' => $this->getUrl($socialModel->get($socialKey), $socialKey),
            ];
        }

        $this->assign('icons', $icons);

        return '<!-- @dataAdmin ' . json_encode(['social' => []]) . ' -->' .
            parent::render();
    }

    private function getUrl(string $url, string $social): string
    {
        if (substr($url, 0, 8) == 'https://') {
            return $url;
        }

        if (substr($url, 0, 7) == 'http://') {
            return $url;
        }

        $socialUrl = [
            'facebook' => 'https://www.facebook.com/',
            'instagram' => 'https://www.instagram.com/',
            'linkedin' => 'https://www.linkedin.com/',
            'youtube' => 'https://www.youtube.com/',
            'pinterest' => 'https://www.pinterest.com/',
        ];

        if (!isset($socialUrl[$social])) {
            return $url;
        }

        $url = ltrim($url, '/');

        return $socialUrl[$social] . $url;
    }
}