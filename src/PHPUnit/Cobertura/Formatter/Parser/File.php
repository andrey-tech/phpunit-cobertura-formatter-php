<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser;

use RuntimeException;

use function file_get_contents;
use function is_file;
use function is_readable;
use function sprintf;

final class File
{
    private ?string $contents = null;

    public function __construct(
        private readonly string $file
    ) {
    }

    public function contents(): string
    {
        if (null === $this->contents) {
            $this->contents = $this->load();
        }

        return $this->contents;
    }

    public function load(): string
    {
        if (!is_file($this->file) || !is_readable($this->file)) {
            throw new RuntimeException(
                sprintf('Cannot find readable Cobertura XML file "%s".', $this->file)
            );
        }

        $contents = file_get_contents($this->file);

        if (false === $contents) {
            throw new RuntimeException(
                sprintf('Failed get contents of Cobertura XML file "%s".', $this->file)
            );
        }

        return $contents;
    }
}
