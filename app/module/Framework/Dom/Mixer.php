<?php declare(strict_types=1);

namespace Framework\Dom;

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
 * @class Framework\Dom\Mixer
 * @package Framework\Dom
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class Mixer
{
    private array $documents = [];

    /**
     * @param string|null $document
     */
    public function __construct(?string $document = null)
    {
        if ($document) {
            $this->documents[] = $document;
        }
    }

    public function addDocument(string $document): static
    {
        $this->documents[] = $document;

        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}