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
    protected array $registeredKeys = ['id', 'identifier'];

    public function getType(string $type): ?TypeModel
    {
        return $this->getTypeByIdentifier($type);
    }

    /**
     * @param string $type
     * @return TypeModel|null
     * @throws Exception
     */
    public function getTypeByIdentifier(string $type): ?TypeModel
    {
        if ($typeModel = $this->getRegisterModel([
            'identifier' => $type
        ])) {
            return $typeModel;
        }

        $typeResourceModel = new TypeResourceModel;
        $typeResourceModel->load($typeModel = new TypeModel(), $type, 'identifier');

        return $this->setRegisterModel($typeModel);
    }

    /**
     * @param int $typeId
     * @return TypeModel|null
     * @throws Exception
     */
    public function getTypeById(int $typeId): ?TypeModel
    {
        if ($typeModel = $this->getRegisterModel([
            'id' => $typeId
        ])) {
            return $typeModel;
        }

        $typeResourceModel = new TypeResourceModel;
        $typeResourceModel->load($typeModel = new TypeModel(), $typeId, 'id');

        return $this->setRegisterModel($typeModel);
    }
}