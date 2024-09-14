<?php declare(strict_types=1);

namespace Builder\Site\Facade\Manager;

use Builder\Page\Model\Resource\Type;

/**
 * @class Builder\Site\Facade\Manager\Message
 */
class Message
{
    const TYPE_INFO = 0;
    const TYPE_ERROR = 1;
    const TYPE_WARNING = 2;

    public function __construct(private string $message, private int $type = self::TYPE_INFO)
    {
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getMessage();
    }
}