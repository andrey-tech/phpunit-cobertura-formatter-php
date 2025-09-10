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
use AndreyTech\PHPUnit\Cobertura\Formatter\Config\File\Creator;
use Exception;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

use function sprintf;

final class Application
{
    private const int EXIT_CODE_OK = 0;
    private const int EXIT_CODE_ERROR = 1;
    private const int EXIT_CODE_RED_METRICS = 2;
    private const int EXIT_CODE_YELLOW_METRICS = 3;

    private ConsoleOutput $consoleOutput;
    private CommandLine $commandLine;
    private Processor $processor;
    private Stats $stats;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
        $this->commandLine = new CommandLine();
        $this->processor = new Processor(
            $this->consoleOutput,
            $this->commandLine
        );
        $this->stats = new Stats();
    }

    public function run(): int
    {
        $this->stats->start();

        try {
            $exitCode = $this->doRun();
        } catch (Throwable $exception) {
            $exitCode = self::EXIT_CODE_ERROR;
            $this->error(
                sprintf('ERROR: %s', $exception->getMessage())
            );
        }

        $this->stats->finish();
        $this->printStats($exitCode);

        return $exitCode;
    }

    /**
     * @throws Exception
     */
    private function doRun(): int
    {
        $this->commandLine->buildInputArgs();

        $this->consoleOutput->getFormatter()->setDecorated(!$this->commandLine->optionNoColor());

        if ($this->commandLine->optionInit()) {
            (new Creator())->create();

            return self::EXIT_CODE_OK;
        }

        $this->processor->process();

        return $this->calculateExitCode();
    }

    private function calculateExitCode(): int
    {
        if ($this->processor->colorizerStats->isRed() && !$this->commandLine->optionIgnoreRedMetricsOnExit()) {
            return self::EXIT_CODE_RED_METRICS;
        }

        if ($this->processor->colorizerStats->isYellow() && !$this->commandLine->optionIgnoreYellowMetricsOnExit()) {
            return self::EXIT_CODE_YELLOW_METRICS;
        }

        return self::EXIT_CODE_OK;
    }

    private function printStats(int $exitCode): void
    {
        $this->consoleOutput->writeln(
            sprintf(
                'Exit code: %s, Time: %s, Memory: %s.',
                $exitCode,
                $this->stats->time(),
                $this->stats->memory()
            )
        );
    }

    private function error(string $message): void
    {
        $this->consoleOutput->writeln(
            sprintf('<fg=red;options=bold>%s</>', $message)
        );
    }
}
