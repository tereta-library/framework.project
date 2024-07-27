<?php declare(strict_types=1);

namespace Framework\Dom;

use Framework\Dom\Node\Tag as NodeTag;
use Framework\Dom\Node\Text as NodeText;
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
     * @param string $document
     */
    public function __construct(private string $document)
    {
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getTree(): array
    {
        $pointer = null;
        $rootArray = [];
        foreach($this->fetchTags() as $tag) {
            $this->processTag($rootArray, $pointer, $tag);
        }

        return $rootArray;
    }

    private function processTag(array &$rootArray, &$pointer, NodeInterface $tag): void
    {
        if ($tag instanceof NodeText && $pointer) {
            $node = new Node();
            $node->setTag($tag);
            $pointer->addChildren($node);
            $node->setParent($pointer);
            return;
        } else if ($tag instanceof NodeText) {
            $node = new Node();
            $node->setTag($tag);
            $rootArray[] = $node;
            return;
        }

        switch($tag->getType()) {
            case NodeTag::TAG_OPEN:
                $node = new Node();
                $node->setTagOpen($tag);
                if ($pointer) {
                    $pointer->addChildren($node);
                    $node->setParent($pointer);
                } else {
                    $rootArray[] = $node;
                }
                $pointer = $node;
                break;
            case NodeTag::TAG_CLOSE:
                if ($pointer->getName() !== $tag->getName()) {
                    throw new Exception('Invalid tag at position ' . $tag->getPositionStart() . ' in document');
                }
                $pointer->setTagClose($tag);
                $pointer = $pointer->getParent();
                break;
            case NodeTag::TAG_SELF_CLOSE:
                $node = new Node();
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
     * @return array
     * @throws Exception
     */
    public function fetchTags(): array
    {
        $array = [];
        while($this->position !== null) {
            if ($text = $this->parseText()) $array[] = $text;
            if ($this->position === null) break;

            $tag = $this->parseTag();
            $array[] = $tag;
        }

        return $array;
    }

    /**
     * @return NodeTag|null
     * @throws Exception
     */
    private function parseTag(): ?NodeTag
    {
        $initialPosition = $this->position;

        $tagType = NodeTag::TAG_NOTSET;
        $openTag = preg_match('/^\<\s*([\w_-]+)(\s|\>){1}/Usi', substr($this->document, $initialPosition),$matchesOpen);
        $closeTag = preg_match('/^\<\s*\/\s*([\w_-]+)(\s|\>){1}/Usi', substr($this->document, $initialPosition), $matchesClose);

        if (!$openTag && !$closeTag) {
            throw new Exception('Invalid tag at position ' . $initialPosition . ' in document');
        }

        if ($openTag) {
            $tagType = NodeTag::TAG_OPEN;
            $matches = $matchesOpen;
        } elseif ($closeTag) {
            $tagType = NodeTag::TAG_CLOSE;
            $matches = $matchesClose;
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
            throw new Exception('Invalid tag at position ' . $offset . ' in document');
        }

        $offset = $offset + strlen($matches[0]);

        $this->position = $offset;

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

        $offsetInside = 0;
        while (true) {
            $offsetInside = strpos($string, $quote, $offsetInside);
            $escapeCount = 0;
            while (substr($string, $offsetInside - 1 - $escapeCount, 1) === '\\') {
                $escapeCount++;
            }

            if ($escapeCount % 2 === 0) {
                break;
            }

            $offsetInside = $offsetInside + 1;
        }
        $offset = $offset + $offsetInside + 1;

        return substr($document, $initialOffset, $offsetInside);
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
}