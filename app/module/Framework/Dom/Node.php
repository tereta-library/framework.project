<?php declare(strict_types=1);

namespace Framework\Dom;

use Framework\Dom\Interface\Node as NodeInterface;
use Framework\Dom\Node\Tag as NodeTag;
use Framework\Dom\Node\Text as NodeText;

/**
 * ···························WWW.TERETA.DEV······························
 * ·······································································
 * : _____                        _                     _                :
 * :|_   _|   ___   _ __    ___  | |_    __ _        __| |   ___  __   __:
 * :  | |    / _ \ | '__|  / _ \ | __|  / _` |      / _` |  / _ \ \ \ / /:
 * :  | |   |  __/ | |    |  __/ | |_  | (_| |  _  | (_| | |  __/  \ V / :
 * :  |_|    \___| |_|     \___|  \__|  \__,_| (_)  \__,_|  \___|   \_/  :
 * ·······································································
 * ·······································································
 *
 * @class Framework\Dom\Node
 * @package Framework\Dom
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class Node
{
    const TYPE_TAG = 1;
    const TYPE_TEXT = 2;

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
        if (!$this->tagOpen) {
            return '';
        }

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

    /**
     * @return Node|null
     */
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
     * @return $this
     */
    public function clearChildren(): static
    {
        $this->children = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getAttribute(string $name): ?string
    {
        if (!$this->tagOpen) return null;
        return $this->tagOpen->getAttribute($name);
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        if ($this->tagOpen instanceof NodeTag) {
            return self::TYPE_TAG;
        }

        if ($this->tagOpen instanceof NodeText) {
            return self::TYPE_TEXT;
        }

        return null;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        if ($this->tagOpen) {
            $tag = $this->tagOpen->render();
        }

        $children = '';
        foreach ($this->getChildren() as $child) {
            $children .= $child->render();
        }

        if ($this->tagClose && $this->tagClose->getType() != $this->tagClose::TAG_SELF_CLOSE) {
            return $tag . $children . $this->tagClose->render();
        }

        return $this->tag->render();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}