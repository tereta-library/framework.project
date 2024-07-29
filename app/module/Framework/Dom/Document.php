<?php declare(strict_types=1);

namespace Framework\Dom;

use Framework\Dom\Node;
use Framework\Dom\Node\Tag as NodeTag;
use Framework\Dom\Node\Text as NodeText;
use Framework\Dom\Node\Comment as NodeComment;
use Exception;
use Framework\Dom\Interface\Node as NodeInterface;

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
 * @class Framework\Dom\Document
 * @package Framework\Dom
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class Document
{
    /**
     * @var int|null $position
     */
    private ?int $position = 0;

    /**
     * @var array|null $tagList
     */
    private ?array $tagList = null;

    /**
     * @var array|null $tree
     */
    private ?array $tree = null;

    /**
     * @param string $document
     * @param string $fileDebug
     */
    public function __construct(private string $document, private string $fileDebug = '')
    {
    }

    /**
     * @param bool $update
     * @return array
     * @throws Exception
     */
    public function getNodeList(bool $documentUpdate = false): array
    {
        $tree = $this->getNodeTree($documentUpdate);

        $nodeList = [];
        foreach($tree as $item) {
            $nodeList[] = $item;
            $this->getNodeListTree($nodeList, $item);
        }

        return $nodeList;
    }

    private function getNodeListTree(array &$nodeList = [], $item)
    {
        foreach ($item->getChildren() as $item) {
            $nodeList[] = $item;
            $this->getNodeListTree($nodeList, $item);
        }
    }

    /**
     * @param bool $update
     * @return array
     * @throws Exception
     */
    public function getNodeTree(bool $update = false): array
    {
        if ($this->tree && !$update) return $this->tree;

        $pointer = null;
        $this->tree = [];
        foreach($this->fetchTags($update) as $tag) {
            $this->processTag($this->tree, $pointer, $tag);
        }

        return $this->tree;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        $return = '';
        foreach ($this->getNodeTree() as $node) {
            $return .= $node->render();
        }

        return $return;
    }


    /**
     * @param bool $update
     * @return array
     * @throws Exception
     */
    public function fetchTags(bool $update = false): array
    {
        if ($this->tagList && !$update) return $this->tagList;

        $this->tagList = [];

        while($this->position !== null) {
            if ($text = $this->parseText()) $this->tagList[] = $text;
            if ($this->position === null) break;

            $tag = $this->parseTag();
            $this->tagList[] = $tag;
        }

        $this->position = null;

        return $this->tagList;
    }

    /**
     * @param array $nodeList
     * @param array $rootArray
     * @param $pointer
     * @param NodeInterface $tag
     * @return void
     * @throws Exception
     */
    private function processTag(array &$rootArray, &$pointer, NodeInterface $tag): void
    {
        if ($tag instanceof NodeText && $pointer) {
            $node = new Node($this);
            $node->setTag($tag);
            $pointer->addChildren($node);
            $node->setParent($pointer);
            return;
        } else if ($tag instanceof NodeText) {
            $node = new Node($this);
            $node->setTag($tag);
            $rootArray[] = $node;
            $node->setParent($pointer);
            return;
        } else if ($tag instanceof NodeComment && $pointer) {
            $node = new Node($this);
            $node->setTag($tag);
            $pointer->addChildren($node);
            $node->setParent($pointer);
            return;
        } else if ($tag instanceof NodeComment) {
            $node = new Node($this);
            $node->setTag($tag);
            $rootArray[] = $node;
            $node->setParent($pointer);
            return;
        }

        switch($tag->getType()) {
            case NodeTag::TAG_OPEN:
                $node = new Node($this);
                $node->setTagOpen($tag);
                if ($pointer) {
                    $pointer->addChildren($node);
                    $node->setParent($pointer);
                } else {
                    $rootArray[] = $node;
                }
                $pointer = $node;
                $nodeList[] = $node;
                break;
            case NodeTag::TAG_CLOSE:
                if ($pointer->getName() !== $tag->getName()) {
                    throw new Exception(
                        'Invalid close tag at ' . $this->getErrorDescription($tag->getPositionStart())
                    );
                }
                $pointer->setTagClose($tag);
                $pointer = $pointer->getParent();
                break;
            case NodeTag::TAG_SELF_CLOSE:
                $node = new Node($this);
                $node->setTagSelfClose($tag);
                if ($pointer) {
                    $pointer->addChildren($node);
                    $node->setParent($pointer);
                } else {
                    $rootArray[] = $node;
                }
                break;
        }
    }

    /**
     * @return NodeInterface|null
     * @throws Exception
     */
    private function parseTag(): ?NodeInterface
    {
        $initialPosition = $this->position;

        $tagType = NodeTag::TAG_NOTSET;

        $openTag = preg_match(
            '/^\<\s*([\w_-]+)(\s|\>){1}/Usi',
            substr($this->document, $initialPosition),
            $matchesOpen
        );
        $closeTag = preg_match(
            '/^\<\s*\/\s*([\w_-]+)(\s|\>){1}/Usi',
            substr($this->document, $initialPosition),
            $matchesClose
        );

        $commentTag = (!$openTag && !$closeTag) ? preg_match(
            '/^\<\!--(.*)--\>/Usi',
            substr($this->document, $initialPosition),
            $matchesComment
        ) : null;

        if (!$openTag && !$closeTag && !$commentTag) {
            throw new Exception(
                'Invalid open tag at: ' . $this->getErrorDescription($initialPosition)
            );
        }

        if ($openTag) {
            $tagType = NodeTag::TAG_OPEN;
            $matches = $matchesOpen;
        } elseif ($closeTag) {
            $tagType = NodeTag::TAG_CLOSE;
            $matches = $matchesClose;
        } elseif ($commentTag) {
            $this->position = $this->position + strlen($matchesComment[0]);

            return new NodeComment($matchesComment[1]);
        }

        $tagName = $matches[1];
        $offset = strlen($matches[0]) + $this->position - 1;

        $attributes = [];
        while(preg_match(
            '/^\s*([\w_-]+)\s*=\s*/Usi',
            substr($this->document, $offset),
            $matches
        )) {
            $offset += strlen($matches[0]);
            $attribute = $matches[1];
            $value = $this->parseAttributeValue($this->document, $offset);
            $attributes[$attribute] = $value;
        }

        $tagConclusion = preg_match('/^\s*\>/Usi', substr($this->document, $offset),$matchesSimple);
        $tagConclusionSelfClosed = preg_match('/^\s*\/\s*\>/Usi', substr($this->document, $offset),$matchesSelfClose);

        if ($tagConclusion) {
            $matches = $matchesSimple;
        } elseif ($tagConclusionSelfClosed) {
            $matches = $matchesSelfClose;
            $tagType = NodeTag::TAG_SELF_CLOSE;
        } else {
            throw new Exception(
                'Invalid tag at: ' . $this->getErrorDescription($offset)
            );
        }

        $this->position = $offset + strlen($matches[0]);

        return (new NodeTag)->setName($tagName)->setPosition($initialPosition, $this->position)
            ->setAttributes($attributes)->setType($tagType);
    }

    /**
     * @param string $document
     * @param int $offset
     * @return string
     */
    private function parseAttributeValue(string &$document, int &$offset): string
    {
        $quote = substr($document, $offset, 1);
        $offset++;
        $initialOffset = $offset;
        $string = substr($document, $offset);

        $offsetInside = strpos($string, $quote);
        $offset = $offset + $offsetInside + 1;

        $value = substr($document, $initialOffset, $offsetInside);
        $value = str_replace('\\' . $quote, $quote, $value);

        return $value;
    }

    /**
     * @return NodeText|null
     */
    private function parseText(): ?NodeText
    {
        $initialPosition = $this->position;
        $position = strpos($this->document, '<', $initialPosition);
        if ($position === false) {
            $this->position = null;
            $tag = substr($this->document, $initialPosition);
            if (!trim($tag)) return null;
            return new NodeText($tag);
        }

        $this->position = $position;
        $tag = substr($this->document, $initialPosition, $position - $initialPosition);

        if (!trim($tag)) return null;

        return new NodeText($tag);
    }

    /**
     * @param int $position
     * @return string
     */
    private function getErrorDescription(int $position): string
    {
        $string = substr($this->document, 0, $position);
        $lines = substr_count($string, "\n");
        $col = strlen($string) - strrpos($string, "\n");

        if ($this->fileDebug) return 'file: ' . $this->fileDebug . ', line: ' . $lines . ', column: ' . $col;
        return 'line: ' . $lines . ', column: ' . $col;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString(): string
    {
        return $this->render();
    }
}