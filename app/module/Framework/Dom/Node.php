<?php declare(strict_types=1);

namespace Framework\Dom;

use Framework\Dom\Interface\Node as NodeInterface;

/**
 * Framework\Dom\Node
 */
class Node
{
    /**
     * @var NodeInterface|null
     */
    private ?NodeInterface $tagOpen = null;

    /**
     * @var NodeInterface|null
     */
    private ?NodeInterface $tagClose = null;

    /**
     * @var NodeInterface|null $tag
     */
    private ?NodeInterface $tag = null;

    /**
     * @var array $children
     */
    private array $children = [];

    /**
     * @var Node|null
     */
    private ?Node $parent = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->tagOpen->getName();
    }

    /**
     * @param NodeInterface $tag
     * @return $this
     */
    public function setTagOpen(NodeInterface $tag): static
    {
        $this->tag = $tag;
        $this->tagOpen = &$this->tag;

        return $this;
    }

    /**
     * @param NodeInterface $tag
     * @return $this
     */
    public function setTagClose(NodeInterface $tag): static
    {
        $this->tagClose = $tag;

        return $this;
    }

    /**
     * @param NodeInterface $tag
     * @return $this
     */
    public function setTag(NodeInterface $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @param NodeInterface $tag
     * @return $this
     */
    public function setTagSelfClose(NodeInterface $tag): static
    {
        $this->tag = $tag;

        $this->tagOpen = &$this->tag;
        $this->tagClose = &$this->tag;

        return $this;
    }

    /**
     * @param Node $parent
     * @return $this
     */
    public function setParent(Node $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @param Node $node
     * @return $this
     */
    public function addChildren(self $node): static
    {
        $this->children[] = $node;
        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function render(): string
    {
        if ($this->tagOpen) {
            $tag = $this->tagOpen->render();
        }

        $children = '';
        foreach ($this->getChildren() as $child) {
            $children .= $child->render();
        }

        if ($this->tagClose) {
            return $tag . $children . $this->tagClose->render();
        }

        return $this->tag->render();
    }

    public function __toString(): string
    {
        return $this->render();
    }
}