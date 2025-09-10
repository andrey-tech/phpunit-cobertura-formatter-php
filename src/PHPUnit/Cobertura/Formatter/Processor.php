<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter;

use AndreyTech\PHPUnit\Cobertura\Formatter\Config\CommandLine;
use AndreyTech\PHPUnit\Cobertura\Formatter\Config\File as ConfigFile;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\File as CoberturaFile;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\Filter\ClassName as ClassNameFilter;
use AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer;
use AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer\Stats as ColorizerStats;
use Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

final readonly class Processor
{
    public ColorizerStats $colorizerStats;

    public function __construct(
        private ConsoleOutput $consoleOutput,
        private CommandLine $commandLine
    ) {
        $this->colorizerStats = new ColorizerStats();
    }

    /**
     * @throws Exception
     */
    public function process(): void
    {
        (new Renderer(
            $this->consoleOutput,
            new Colorizer(
                $this->colorizerStats,
                new ConfigFile(
                    $this->commandLine->optionConfigFile()
                )
            )
        ))->render(
            (new ClassNameFilter(
                $this->commandLine->optionFilterClassName()
            ))->filter(
                (new Parser())->parse(
                    new CoberturaFile(
                        $this->commandLine->coberturaFile()
                    )
                )
            )
        );
    }
}
