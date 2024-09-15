<?php declare(strict_types=1);

namespace Builder\User\Model;

use Framework\User\Model\User as UserExtendedModel;

class User extends UserExtendedModel
{
    public function loadBySiteIdentifier(int $siteId, string $identifier): static
    {
        $this->resourceModel->load($this, [
            'siteId'     => $siteId,
            'identifier' => $identifier
        ]);
        return $this;
    }
}