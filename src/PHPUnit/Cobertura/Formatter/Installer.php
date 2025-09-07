<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter;

use function copy;
use function file_exists;
use function getcwd;
use function sprintf;

use const PHP_EOL;

final class Installer
{
    private const string CONFIG_FILE = 'phpunit-cobertura-formatter.yml.dist';

    public static function postInstall(): void
    {
        self::copyConfig();
    }

    public static function postUpdate(): void
    {
        self::copyConfig();
    }

    private static function copyConfig(): void
    {
        $source = sprintf('%s/Config/File/%s', __DIR__, self::CONFIG_FILE);
        $destination = sprintf('%s/%s', (string) getcwd(), self::CONFIG_FILE);

        if (file_exists($destination)) {
            return;
        }

        echo sprintf('Copying default config file to "%s"...', $destination) . PHP_EOL;
        if (!copy($source, $destination)) {
            echo sprintf('Failed to copy default config file to "%s"...', $destination) . PHP_EOL;
        }
    }
}
