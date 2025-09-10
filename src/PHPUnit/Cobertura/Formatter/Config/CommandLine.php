<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Config;

use RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class CommandLine
{
    private ArgvInput $input;

    public function __construct()
    {
        $this->input = new ArgvInput();
    }

    public function buildInputArgs(): void
    {
        $definition = new InputDefinition();

        $definition->addArguments([
            new InputArgument('cobertura-file', InputArgument::OPTIONAL),
        ]);

        $definition->addOptions([
            new InputOption('init', null, InputOption::VALUE_NONE),
            new InputOption('filter-class-name', null, InputOption::VALUE_REQUIRED),
            new InputOption('config-file', null, InputOption::VALUE_REQUIRED),
            new InputOption('no-color', null, InputOption::VALUE_NONE),
            new InputOption('ignore-red-metrics-on-exit', null, InputOption::VALUE_NONE),
            new InputOption('ignore-yellow-metrics-on-exit', null, InputOption::VALUE_NONE)
        ]);

        $this->input = new ArgvInput(null, $definition);
    }

    public function coberturaFile(): string
    {
        $coberturaFile = (string) $this->input->getArgument('cobertura-file');

        if ('' === $coberturaFile) {
            throw new RuntimeException('Missing required argument: path to cobertura XML file.');
        }

        return $coberturaFile;
    }

    public function optionInit(): bool
    {
        return (bool) $this->input->getOption('init');
    }

    public function optionNoColor(): bool
    {
        return (bool) $this->input->getOption('no-color');
    }

    public function optionIgnoreRedMetricsOnExit(): bool
    {
        return (bool) $this->input->getOption('ignore-red-metrics-on-exit');
    }

    public function optionIgnoreYellowMetricsOnExit(): bool
    {
        return (bool) $this->input->getOption('ignore-yellow-metrics-on-exit');
    }

    public function optionFilterClassName(): ?string
    {
        /** @var string|null $filterClassName */
        $filterClassName = $this->input->getOption('filter-class-name');

        if (null === $filterClassName) {
            return null;
        }

        if ('' === $filterClassName) {
            throw new RuntimeException('The "--filter-class-name" option requires a value.');
        }

        return $filterClassName;
    }

    public function optionConfigFile(): ?string
    {
        /** @var string|null $configFile */
        $configFile = $this->input->getOption('config-file');

        if (null === $configFile) {
            return null;
        }

        if ('' === $configFile) {
            throw new RuntimeException('The "--config-file" option requires a value.');
        }

        return $configFile;
    }
}
