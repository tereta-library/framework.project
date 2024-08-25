<?php declare(strict_types=1);

namespace Builder\Content\Cli;

use Framework\Cli\Interface\Controller;
use Builder\Site\Model\Site as SiteModel;
use Builder\Site\Model\Resource\Site as SiteResourceModel;
use Framework\Cli\Symbol;
use Exception;

/**
 * @class Builder\Content\Cli\Make
 */
class Make implements Controller
{
    /**
     * @cli make:content:sample
     * @cliDescription Install site sample data
     * @return void
     * @throws Exception
     */
    public function make(): void
    {
        // Make a new site
        $siteResourceModel = (new SiteResourceModel);
        $siteResourceModel->load($siteModel = new SiteModel(), 'sample', 'identifier');

        $siteId = $siteModel->get('id');
        if (!$siteId) {
            throw new Exception("The \"sample\" site not found");
        }
        echo Symbol::COLOR_GREEN . "The \"{$siteModel->get('identifier')}\" [{$siteId}] site will be used for the sample content page\n" . Symbol::COLOR_RESET;


    }
}