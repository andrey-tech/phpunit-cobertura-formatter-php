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
use AndreyTech\PHPUnit\Cobertura\Formatter\Config\File\Creator;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\File as CoberturaFile;
use AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer;
use Exception;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

use function sprintf;

final class Application
{
    private const int EXIT_CODE_OK = 0;
    private const int EXIT_CODE_ERROR = 1;

    private ConsoleOutput $consoleOutput;
    private CommandLine $commandLine;
    private Stats $stats;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
        $this->commandLine = new CommandLine();
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

    /**
     * @throws Exception
     */
    private function doRun(): int
    {
        $this->consoleOutput->getFormatter()->setDecorated(!$this->commandLine->optionNoColor());

        if ($this->commandLine->optionInit()) {
            (new Creator())->create();

            return self::EXIT_CODE_OK;
        }

        $this->parseAndRender();

        return self::EXIT_CODE_OK;
    }

    /**
     * @throws Exception
     */
    private function parseAndRender(): void
    {
        (new Renderer(
            $this->consoleOutput,
            new Colorizer(
                new ConfigFile()
            )
        ))->render(
            (new Parser())->parse(
                new CoberturaFile(
                    $this->commandLine->coberturaFile()
                )
            )
        );
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
