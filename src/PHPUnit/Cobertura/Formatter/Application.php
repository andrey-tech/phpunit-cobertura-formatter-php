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
use AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

use function sprintf;

final class Application
{
    private const int EXIT_CODE_OK = 0;
    private const int EXIT_CODE_ERROR = 1;

    private ConsoleOutput $consoleOutput;
    private Stats $stats;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
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
        $this->showStats($exitCode);

        return $exitCode;
    }

    private function doRun(): int
    {
        (new Renderer(
            $this->consoleOutput,
            new Colorizer(
                new ConfigFile()
            )
        ))->render(
            (new Parser())->parse(
                new CoberturaFile(
                    (new CommandLine())->coberturaFile()
                )
            )
        );

        return self::EXIT_CODE_OK;
    }

    private function message(string $message): void
    {
        $this->consoleOutput->writeln($message);
    }

    private function error(string $message): void
    {
        $this->consoleOutput->writeln(
            sprintf('<fg=red;options=bold>%s</>', $message)
        );
    }

    private function showStats(int $exitCode): void
    {
        $this->message(
            sprintf(
                'Exit code: %s, Time: %s, Memory: %s.',
                $exitCode,
                $this->stats->time(),
                $this->stats->memory()
            )
        );
    }
}
