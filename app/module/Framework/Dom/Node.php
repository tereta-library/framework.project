<?php declare(strict_types=1);

namespace Framework\Dom;

use Framework\Dom\Interface\Node as NodeInterface;
use Framework\Dom\Node\Tag as NodeTag;
use Framework\Dom\Node\Text as NodeText;
use Framework\Dom\Node\Comment as NodeComment;
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
    const int TYPE_TAG = 1;
    const int TYPE_TEXT = 2;
    const int TYPE_COMMENT = 3;

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
     * @var int
     */
    private int $nodeIndex = 0;

    /**
     * @var int
     */
    private static int $nodeStaticIndex = 0;

    public function __construct(private Document &$document)
    {
        $this->nodeIndex = static::$nodeStaticIndex;
        static::$nodeStaticIndex++;
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
     * @return NodeInterface
     */
    public function getTag(): NodeInterface
    {
        return $this->tag;
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
        $newNode->setParent($this);

        foreach ($this->children as $key => $child) {
            if ($child === $oldNode) {
                $this->children[$key] = $newNode;
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
     * @return $this
     */
    public function remove($element = null): static
    {
        if (!$element) {
            $this->getParent()->remove($this);

            return $this;
        }

        foreach ($this->children as $key=>$child) {
            if ($child === $element) {
                unset($this->children[$key]);
            }
        }

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
     * @return array
     */
    public function getAttributes(): array
    {
        if (!$this->tagOpen) return [];
        return $this->tagOpen->getAttributes();
    }

    public function setAttribute(string $name, ?string $value): static
    {
        if (!$this->tagOpen) return $this;
        $this->tagOpen->setAttribute($name, $value);

        return $this;
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

        if ($this->tag instanceof NodeComment) {
            return self::TYPE_COMMENT;
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
            if (!in_array($param, array_keys($data))) {
                throw new Exception('Missing parameter: ' . $param);
            }
        }

        $this->tagOpen = $data['tagOpen'];
        $this->tagClose = $data['tagClose'];
        $this->tag = $data['tag'];
        $this->children = $data['children'];
        foreach ($this->children as $item) {
            $item->setParent($this);
        }

        $this->parent = $data['parent'];

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        if ($this->tagOpen) {
            $tag = $this->tagOpen->render() . "\n";
        }

        $children = '';
        foreach ($this->getChildren() as $child) {
            $children .= $child->render();
        }

        if ($this->tagClose && $this->tagClose->getType() != $this->tagClose::TAG_SELF_CLOSE) {
            return $tag . $children . $this->tagClose->render() . "\n";
        }

        return $this->tag->render() . "\n";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}