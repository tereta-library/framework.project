<?php declare(strict_types=1);

namespace Builder\Site\Cli;

use Builder\Site\Facade\Manager as SiteManagerFacade;
use Framework\Cli\Interface\Controller;
use Framework\Cli\Symbol;
use Exception;

/**
 * @class Builder\Site\Cli\Make
 */
class Make implements Controller
{
    /**
     * @cli make:sample:site
     * @cliDescription Install site sample data
     * @return void
     * @throws Exception
     */
    public function make(): void
    {
        $identifier = 'sample';
        $site = [
            'name' => 'Sample Site',
            'tagline' => 'Sample Tagline',
            'phone' => '+1234567890',
            'email' => 'sample@localhost',
            'address' => 'Sample Address',
            'copyright' => '© 2024 Tereta Alexander (www.tereta.dev)',
        ];

        $sitePrimaryDomain = 'sample.localhost';

        $administrator = [
            'email' => "admin@sample.localhost",
            'password' => "admin"
        ];

        foreach((new SiteManagerFacade)->createSite($identifier, $site, $sitePrimaryDomain, $administrator) as $message) {
            switch ($message->getType()) {
                case($message::TYPE_INFO):
                    echo Symbol::COLOR_GREEN . $message . Symbol::COLOR_RESET . "\n";
                    break;
                default:
                    echo $message;
            }
        }
        echo Symbol::COLOR_GREEN . "Primary domain: {$sitePrimaryDomain}.\n" . Symbol::COLOR_RESET;
        echo Symbol::COLOR_GREEN . "Administrator email: {$administrator['email']}.\n" . Symbol::COLOR_RESET;
        echo Symbol::COLOR_GREEN . "Administrator password: {$administrator['password']}.\n" . Symbol::COLOR_RESET;
    }
}