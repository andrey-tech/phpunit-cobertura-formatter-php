<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Config;

use AndreyTech\PHPUnit\Cobertura\Formatter\Config\File\Validator;
use JsonException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

use function array_key_exists;
use function array_shift;
use function count;
use function explode;
use function getcwd;
use function implode;
use function is_array;
use function is_file;
use function is_readable;
use function sprintf;

final class File
{
    private const string SEPARATOR = '.';

    /**
     * @var list<string>
     */
    private const array DEFAULT_FILES = [
        'phpunit-cobertura-formatter.yml',
        'phpunit-cobertura-formatter.yml.dist',
    ];

    private Validator $validator;

    /**
     * @var string[]
     */
    private array $files;

    /**
     * @var array<mixed>|null
     */
    private ?array $config = null;

    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * @param string[]|null $files
     */
    public function __construct(?array $files = null)
    {
        $this->validator = new Validator();
        $this->files = $files ?? self::DEFAULT_FILES;
    }

    /**
     * @throws JsonException
     */
    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->getValueFromArray(
                $this->all(),
                $key
            );
        }

        return $this->cache[$key];
    }

    /**
     * @return array<mixed>
     *
     * @throws JsonException
     */
    private function all(): array
    {
        if (null === $this->config) {
            $this->config = $this->load(
                $this->findFile()
            );

            $this->validator->validate($this->config);
        }

        return $this->config;
    }

    private function findFile(): string
    {
        foreach ($this->files as $fileName) {
            $file = sprintf('%s/%s', (string) getcwd(), $fileName);

            if (!is_file($file)) {
                continue;
            }

            if (!is_readable($file)) {
                throw new RuntimeException(
                    sprintf('Cannot read config YAML file "%s".', $file)
                );
            }

            return $file;
        }

        throw new RuntimeException(
            sprintf('Cannot find any config YAML files: "%s".', implode(', ', $this->files))
        );
    }

    /**
     * @return array<mixed>
     */
    private function load(string $file): array
    {
        return (array) Yaml::parseFile($file);
    }

    /**
     * @param array<array-key, mixed> $from
     */
    private function getValueFromArray(array $from, string $path): mixed
    {
        if (array_key_exists($path, $from)) {
            return $from[$path];
        }

        $keys = explode(self::SEPARATOR, $path);

        $key = array_shift($keys);

        /** @var mixed $value */
        $value = $from[$key] ?? null;

        if (null === $value) {
            return null;
        }

        if (is_array($value) && count($keys) > 0) {
            return $this->getValueFromArray($value, implode(self::SEPARATOR, $keys));
        }

        return null;
    }
}
