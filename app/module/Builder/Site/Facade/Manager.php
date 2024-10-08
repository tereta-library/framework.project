<?php declare(strict_types=1);

namespace Builder\Site\Facade;

use Builder\Site\Facade\Manager\Message;
use Builder\Site\Model\Domain as DomainModel;
use Builder\Site\Model\Resource\Domain as DomainResourceModel;
use Builder\Site\Model\Resource\Site as SiteResourceModel;
use Builder\Site\Model\Resource\User as SiteUserResourceModel;
use Builder\Site\Model\Site as SiteModel;
use Builder\Site\Model\User as SiteUserModel;
use Framework\Cli\Symbol;
use Framework\Database\Singleton as SingletonDatabase;
use Framework\User\Model\Resource\User as UserResourceModel;
use Framework\User\Model\User as UserModel;
use Exception;

/**
 * Generated by www.Tereta.dev on 2024-09-13 20:13:15
 * @class Builder\Site\Facade\Manager
 */
class Manager
{
    /**
     * @param string $identifier
     * @param array $site
     * @param string $sitePrimaryDomain
     * @param array $administrator
     * @return void
     * @throws Exception
     */
    public function createSite(string $identifier, array $site, string $sitePrimaryDomain, array $administrator): \Generator
    {
        try {
            SingletonDatabase::getConnection()->beginTransaction();

            $siteResourceModel = (new SiteResourceModel);
            $siteResourceModel->load($siteModel = new SiteModel(), $identifier, 'identifier');

            if ($siteModel->get('id')) {
                throw new Exception("The \"{$siteModel->get('identifier')}\" site already exists");
            }

            $siteModel->set('identifier', $identifier);

            $siteModel->set('name', $site['name']);
            $siteModel->set('tagline', $site['tagline']);
            $siteModel->set('phone', $site['phone']);
            $siteModel->set('email', $site['email']);
            $siteModel->set('address', $site['address']);
            $siteModel->set('copyright', $site['copyright']);
            $siteResourceModel->save($siteModel);

            $siteId = $siteModel->get('id');
            yield new Message("The \"{$siteModel->get('identifier')}\" site will be created with the \"{$siteId}\" ID", Message::TYPE_INFO);

            // Make a new domain
            $domainResourceModel = (new DomainResourceModel);
            $domainResourceModel->where('primaryDomain = 1')->load($domainModel = new DomainModel(), $siteId, 'siteId');

            $domainModel->set('siteId', $siteId);
            $domainModel->set('primaryDomain', 1);
            $domainModel->set('domain', $sitePrimaryDomain);
            $domainResourceModel->save($domainModel);
            yield new Message("The \"{$domainModel->get('domain')}\" domain will be created with the \"{$domainModel->get('id')}\" ID.", Message::TYPE_INFO);

            // Make a new user
            $userResourceModel = (new UserResourceModel);
            $userModel = new UserModel();
            $userIdentifier = "{$siteId}:{$administrator['email']}";
            $userResourceModel->load($userModel, ['identifier' => $administrator['email'], 'siteId' => $siteId]);
            $userModel->set('siteId', $siteId);
            $userModel->set('identifier', $userIdentifier);
            $userModel->setPassword($administrator['password']);
            $userResourceModel->save($userModel);
            $userId = $userModel->get('id');
            yield new Message("The \"{$administrator['email']}\" user will be created with the \"{$userModel->get('id')}\" ID.", Message::TYPE_INFO);

            // Make relation between site and user
            $siteUserResourceModel = (new SiteUserResourceModel);
            $siteUserModel = new SiteUserModel();
            $siteUserModel->set('siteId', $siteId);
            $siteUserModel->set('userId', $userId);
            $siteUserModel->set('acl', 1);
            $siteUserResourceModel->save($siteUserModel);
            yield new Message("The \"{$siteModel->get('identifier')}\" site is successfully created.", Message::TYPE_INFO);
            yield new Message("The \"{$administrator['email']}\" user is related to the \"{$siteModel->get('identifier')}\" site.", Message::TYPE_INFO);
            SingletonDatabase::getConnection()->commit();
        } catch (Exception $e) {
            SingletonDatabase::getConnection()->rollBack();
            throw $e;
        }
    }
}