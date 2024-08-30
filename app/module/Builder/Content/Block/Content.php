<?php declare(strict_types=1);

namespace Builder\Content\Block;

use Builder\Content\Model\Content as ModelContent;
use Framework\View\Php\Template;
use Exception;

/**
 * @class Builder\Content\Block\Content
 */
class Content extends Template
{
    /**
     * @var ModelContent|null
     */
    private ?ModelContent $contentModel = null;

    /**
     * @param ModelContent $contentModel
     * @return $this
     */
    public function setModel(ModelContent $contentModel): static
    {
        $this->contentModel = $contentModel;
        $this->assign('content', $contentModel->get('content'));
        $this->assign('header', $contentModel->get('header'));

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        return '<!-- @dataAdmin ' . json_encode(['content' => $this->contentModel->get('identifier')]) . ' -->' .
            parent::render();
    }
}