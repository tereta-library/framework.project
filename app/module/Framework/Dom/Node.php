<?php declare(strict_types=1);

namespace Framework\Dom;

use Framework\Dom\Interface\Node as NodeInterface;
use Framework\Dom\Node\Tag as NodeTag;
use Framework\Dom\Node\Text as NodeText;
use Exception;

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

    public function __construct(private Document &$document)
    {
    }

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

    public function replaceChild(Node $oldNode, Node $newNode): static
    {
        $newNode->setParent($oldNode->getParent());
        $children = $this->getChildren();
        $newChildren = [];
        foreach ($children as $child) {
            if ($child === $oldNode) {
                $newChildren[] = $newNode;
            } else {
                $newChildren[] = $child;
            }
        }

        $this->clearChildren();
        foreach ($newChildren as $child) {
            $this->addChildren($child);
        }

        foreach ($this->document->getNodeList() as $key => $node) {
            if ($node === $oldNode) {
                $this->document->setNodeListItem($key, $newNode);
            }
        }

        return $this;
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
     * @return array
     */
    public function export(): array
    {
        return [
            'tagOpen' => $this->tagOpen,
            'tagClose' => $this->tagClose,
            'tag' => $this->tag,
            'children' => $this->children,
            'parent' => $this->parent,
        ];
    }

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function import(array $data): static
    {
        $params = [
            'tagOpen',
            'tagClose',
            'tag',
            'children',
            'parent',
        ];

        foreach ($params as $param) {
            if (!isset($data[$param])) {
                throw new Exception('Missing parameter: ' . $param);
            }
        }

        $this->tagOpen = $data['tagOpen'];
        $this->tagClose = $data['tagClose'];
        $this->tag = $data['tag'];
        $this->children = $data['children'];
        $this->parent = $data['parent'];

        return $this;
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