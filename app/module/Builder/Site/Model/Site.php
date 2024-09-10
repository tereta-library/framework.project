<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Framework\Database\Abstract\Model;
use Framework\User\Model\User as UserModel;
use Framework\Application\Manager as ApplicationManager;
use Exception;
use Builder\Site\Model\Domain as DomainModel;
use Builder\Site\Model\Site\Configuration\Repository as ConfigurationRepository;

/**
 * @class Builder\Site\Model\Site
 */
class Site extends Model
{
    /**
     * @var UserModel $userModel
     */
    private UserModel $userModel;

    /**
     * @var Domain $domainModel
     */
    private DomainModel $domainModel;

    /**
     * @var ApplicationManager $applicationManager
     */
    private ApplicationManager $applicationManager;

    /**
     * @var null|ConfigurationRepository $configurationRepository
     */
    private ?ConfigurationRepository $configurationRepository = null;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->applicationManager = ApplicationManager::getInstance();
        parent::__construct($data);
    }

    /**
     * @return ConfigurationRepository
     * @throws Exception
     */
    public function getConfig(): ConfigurationRepository
    {
        if ($this->configurationRepository) {
            return $this->configurationRepository;
        }
        return $this->configurationRepository = ConfigurationRepository::getSiteInstance($this->get('id'));
    }

    /**
     * @param UserModel $userModel
     * @return $this
     */
    public function setUserModel(UserModel $userModel): static
    {
        $this->userModel = $userModel;
        return $this;
    }

    /**
     * @return UserModel
     */
    public function getUserModel(): UserModel
    {
        return $this->userModel;
    }

    /**
     * @return DomainModel
     * @throws Exception
     */
    public function getDomainModel(): DomainModel
    {
        return $this->domainModel;
    }

    public function setDomainModel(DomainModel $domainModel): static
    {
        $this->domainModel = $domainModel;
        return $this;
    }

    /**
     * @param array $files
     * @return $this
     * @throws Exception
     */
    public function setFiles(array $files): static
    {
        if (isset($files['iconImage'])) {
            $this->uploadFile('iconImage', $files['iconImage']);
        }
        if (isset($files['logoImage'])) {
            $this->uploadFile('logoImage', $files['logoImage']);
        }
        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPublicData(): array
    {
        $data = $this->getData();
        $data['iconImage'] = $this->getIconImageUrl();
        $data['logoImage'] = $this->getLogoImageUrl();

        return $data;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getIconImageFile(): ?string
    {
        return $this->get('iconImage') ? $this->getMedia()->getPath($this->get('iconImage')): null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getLogoImageFile(): ?string
    {
        return $this->get('logoImage') ? $this->getMedia()->getPath($this->get('logoImage')): null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getIconImageUrl(): ?string
    {
        return $this->get('iconImage') ? $this->getMedia()->getUrl($this->get('iconImage')) : null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getLogoImageUrl(): ?string
    {
        return $this->get('logoImage') ? $this->getMedia()->getUrl($this->get('logoImage')) : null;
    }

    /**
     * @param string $key
     * @param array $file
     * @return $this
     * @throws Exception
     */
    private function uploadFile(string $key, array $file): static
    {
        if (!in_array(
            $file['type'],
            ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml', 'image/webp', 'image/avif']
        )) {
            throw new Exception("Invalid file type {$file['type']}");
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = "logo/{$key}.{$fileExtension}";
        $fileTarget = $this->getMedia()->getPath($fileName);
        $dirTarget = dirname($fileTarget);
        if (!is_dir($dirTarget)) mkdir($dirTarget, 0777, true);

        if (!move_uploaded_file($file['tmp_name'], $fileTarget)) {
            throw new Exception("File upload error {$file['name']}");
        }

        $this->set($key, $fileName);
        return $this;
    }

    /**
     * @return Media
     * @throws Exception
     */
    public function getMedia(): Media
    {
        static $media = null;
        if ($media) return $media;

        if (!$this->get('id')) {
            throw new Exception("Entity ID is not set to use media class in the site model");
        }

        return $media = new Media(
            $this,
            $this->applicationManager->getConfig()->get('publicMedia'),
            $this->applicationManager->getConfig()->get('publicMediaUri')
        );
    }
}