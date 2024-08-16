<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource\Domain;

use Framework\Database\Abstract\Resource\Collection as ResourceCollection;
use Builder\Site\Model\Resource\Domain as ResourceModel;
use Builder\Site\Model\Domain as Model;

/**
 * @class Builder\Site\Model\Resource\Domain\Collection
 */
class Collection extends ResourceCollection
{
    public function __construct()
    {
        parent::__construct(ResourceModel::class, Model::class);
    }
}