<?php declare(strict_types=1);

namespace Builder\Site\Command;

use Framework\Cli\Interface\Controller;
use Builder\Site\Model\Entity as SiteModel;
use Builder\Site\Model\Resource\Entity as SiteResourceModel;
use Builder\Site\Model\Domain as DomainModel;
use Builder\Site\Model\Resource\Domain as DomainResourceModel;
use Framework\Cli\Symbol;
use Framework\User\Model\User as UserModel;
use Framework\User\Model\Resource\User as UserResourceModel;
use Builder\Site\Model\User as SiteUserModel;
use Builder\Site\Model\Resource\User as SiteUserResourceModel;

/**
 * @class Builder\Site\Command\Make
 */
class Make implements Controller
{
    /**
     * @cli make:site:sample
     * @cliDescription Install site sample data
     * @return void
     * @throws \Exception
     */
    public function make(): void
    {
        // Make a new site
        $siteResourceModel = (new SiteResourceModel);
        $siteResourceModel->load($siteModel = new SiteModel(), 'sample', 'identifier');

        $siteModel->set('identifier', 'sample');
        $siteModel->set('name', 'Sample Site');
        $siteModel->set('tagline', 'This is a sample site');
        $siteModel->set('phone', '+1234567890');
        $siteModel->set('email', 'tereta.alexander@gmail.com');
        $siteModel->set('address', '1234 Sample Street, Sample City, Sample Country');
        $siteModel->set('copyright', '© 2024 Sample by Tereta');
        $siteResourceModel->save($siteModel);

        $siteId = $siteModel->get('id');
        echo Symbol::COLOR_GREEN . "The \"{$siteModel->get('identifier')}\" site created with the \"{$siteId}\" ID\n" . Symbol::COLOR_RESET;

        // Make a new domain
        $domainResourceModel = (new DomainResourceModel);
        $domainResourceModel->where('primaryDomain = 1')->load($domainModel = new DomainModel(), $siteId, 'siteId');

        $domainModel->set('siteId', $siteId);
        $domainModel->set('primaryDomain', 1);
        $domainModel->set('domain', 'sample.localhost');
        $domainResourceModel->save($domainModel);
        echo Symbol::COLOR_GREEN . "The \"{$domainModel->get('domain')}\" domain created with the \"{$domainModel->get('id')}\" ID.\n" . Symbol::COLOR_RESET;

        // Make a new user
        $identifier = 'admin@sample.localhost';
        $userResourceModel = (new UserResourceModel);
        $userModel = new UserModel();
        $userResourceModel->load($userModel, 'admin@sample.localhost', 'identifier');
        $userModel->set('identifier', $identifier);
        $userModel->setPassword('admin');
        $userResourceModel->save($userModel);
        $userId = $userModel->get('id');
        echo Symbol::COLOR_GREEN . "The \"{$userModel->get('identifier')}\" user created with the \"{$userModel->get('id')}\" ID.\n" . Symbol::COLOR_RESET;

        // Make relation between site and user
        $siteUserResourceModel = (new SiteUserResourceModel);
        $siteUserModel = new SiteUserModel();
        $siteUserModel->set('siteId', $siteId);
        $siteUserModel->set('userId', $userId);
        $siteUserResourceModel->save($siteUserModel);
        echo Symbol::COLOR_GREEN . "Relating \"{$userModel->get('identifier')}\" to the \"{$siteModel->get('identifier')}\" site.\n" . Symbol::COLOR_RESET;
    }
}