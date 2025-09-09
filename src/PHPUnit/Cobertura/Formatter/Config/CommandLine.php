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

final readonly class CommandLine
{
    private ArgvInput $input;

    public function __construct()
    {
        $this->input = $this->buildArgvInput();
    }

    public function buildArgvInput(): ArgvInput
    {
        $definition = new InputDefinition();

        $argument = new InputArgument('cobertura-file', InputArgument::OPTIONAL);
        $definition->addArgument($argument);

        $option = new InputOption('init', null, InputOption::VALUE_NONE);
        $definition->addOption($option);

        return new ArgvInput(null, $definition);
    }

    public function coberturaFile(): string
    {
        $coberturaFile = (string) $this->input->getArgument('cobertura-file');

        if ('' === $coberturaFile) {
            throw new RuntimeException('Missing required argument <path to cobertura XML file>.');
        }

        return $coberturaFile;
    }

    public function optionInit(): bool
    {
        return (bool) $this->input->getOption('init');
    }
}
