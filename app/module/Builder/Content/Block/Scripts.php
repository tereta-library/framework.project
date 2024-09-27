<?php declare(strict_types=1);

namespace Builder\Content\Block;

use Builder\Content\Model\Content as ModelContent;
use Framework\View\Php\Template;

/**
 * @class Builder\Content\Block\Scripts
 */
class Scripts extends Template
{
    /**
     * @param ModelContent $contentModel
     * @return $this
     */
    public function setModel(ModelContent $contentModel): static
    {
        $this->contentModel = $contentModel;
        $this->assign('pageCss', "/css/content/{$contentModel->get('identifier')}.css");

        return $this;
    }
}