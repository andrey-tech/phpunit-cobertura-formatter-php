<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Config\File;

use JsonException;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator as JsonSchemaValidator;
use RuntimeException;

use function file_get_contents;
use function implode;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

final class Validator
{
    private const string SCHEMA_FILE = __DIR__ . '/../../../../../../config/phpunit-cobertura-formatter.schema.json';

    /**
     * @param array<mixed> $config
     *
     * @throws JsonException
     */
    public function validate(array $config): void
    {
        $validator = new JsonSchemaValidator();
        $validator->validate(
            $config,
            $this->loadSchema(),
            Constraint::CHECK_MODE_TYPE_CAST
        );

        if (!$validator->isValid()) {
            throw new RuntimeException(
                'Invalid config YAML file:' . PHP_EOL . $this->buildErrorMessage($validator)
            );
        }
    }

    /**
     * @return array<mixed>
     *
     * @throws JsonException
     */
    private function loadSchema(): array
    {
        $schema = file_get_contents(self::SCHEMA_FILE);
        if (false === $schema) {
            throw new RuntimeException(
                sprintf('Failed to load JSON schema file: "%s".', self::SCHEMA_FILE)
            );
        }

        return (array) json_decode(
            json: $schema,
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
    }

    private function buildErrorMessage(JsonSchemaValidator $validator): string
    {
        $violations = [];
        /** @var array<string, string> $error */
        foreach ($validator->getErrors() as $index => $error) {
            $violations[] = sprintf('%u) %s: %s', (int) $index + 1, $error['property'], $error['message']);
        }

        return implode(PHP_EOL, $violations);
    }
}
