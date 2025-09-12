<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Config\File;

use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutput;

use function copy;
use function file_exists;
use function getcwd;
use function sprintf;

final readonly class Creator
{
    private const string CONFIG_DIR = __DIR__ . '/../../../../../../config';
    private const string CONFIG_FILE = 'phpunit-cobertura-formatter.yml.dist';

    public function __construct(
        private ConsoleOutput $consoleOutput
    ) {
    }

    public function create(): void
    {
        $source = sprintf('%s/%s', self::CONFIG_DIR, self::CONFIG_FILE);
        $destination = sprintf('%s/%s', (string) getcwd(), self::CONFIG_FILE);

        if (file_exists($destination)) {
            throw new RuntimeException(
                sprintf('Default config file already exists at "%s".', $destination)
            );
        }

        if (!copy($source, $destination)) {
            throw new RuntimeException(
                sprintf('Failed to copy default config file to "%s".', $destination)
            );
        }

        $this->consoleOutput->writeln(
            sprintf('Default config file created at "%s"', $destination)
        );
    }
}
