<?php declare(strict_types=1);

namespace Builder\Page\Model\Type;

use Framework\Database\Abstract\Repository as RepositoryAbstract;
use Builder\Page\Model\Type as TypeModel;
use Builder\Page\Model\Resource\Type as TypeResourceModel;
use Exception;

/**
 * @class Builder\Page\Model\Type\Repository
 */
class Repository extends RepositoryAbstract
{
    protected static ?RepositoryAbstract $instance = null;

    /**
     * @param string $type
     * @return TypeModel|null
     * @throws Exception
     */
    public function getType(string $type): ?TypeModel
    {
        if ($typeModel = $this->getRegisterModel([
            'type' => $type
        ])) {
            return $typeModel;
        }

        $typeResourceModel = new TypeResourceModel;
        $typeResourceModel->load($typeModel = new TypeModel(), $type, 'identifier');

        return $this->setRegisterModel($typeModel);
    }
}