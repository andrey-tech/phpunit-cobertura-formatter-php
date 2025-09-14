<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace Test\Functional;

use Symfony\Component\Process\Process;

use function getcwd;
use function realpath;
use function sprintf;
use function unlink;

final class ScriptTest extends TestCase
{
    private const string SCRIPT_FILE = __DIR__ . '/../../bin/phpunit-cobertura-formatter';
    private const string CONFIG_FILE = __DIR__ . '/../../config/phpunit-cobertura-formatter.yml.dist';

    public function testSuccessRed(): void
    {
        $process = $this->runProcess([
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-red.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('success-red.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(2, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessRed(): void
    {
        $process = $this->runProcess([
            '--no-color',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-red.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('no-color-success-red.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(2, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessIgnoreRed(): void
    {
        $process = $this->runProcess([
            '--no-color',
            '--ignore-red-metrics-on-exit',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-red.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('no-color-success-red.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(3, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessGreen(): void
    {
        $process = $this->runProcess([
            '--no-color',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-green.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('no-color-success-green.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(0, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessFilterGreen(): void
    {
        $process = $this->runProcess([
            '--no-color',
            '--filter-class-name=Fixer$',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-green.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('no-color-success-green.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(0, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessFilterEmptyGreen(): void
    {
        $process = $this->runProcess([
            '--no-color',
            '--filter-class-name=xxx',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-green.xml')
        ]);

        self::assertStringNotContainsString(
            $this->getDataFileContents('no-color-success-green.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(0, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessYellow(): void
    {
        $process = $this->runProcess([
            '--no-color',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-yellow.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('no-color-success-yellow.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(3, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoColorSuccessIgnoreYellow(): void
    {
        $process = $this->runProcess([
            '--no-color',
            '--ignore-yellow-metrics-on-exit',
            sprintf('--config-file=%s', (string) realpath(self::CONFIG_FILE)),
            $this->getDataFileAbsolutePath('cobertura-yellow.xml')
        ]);

        self::assertStringContainsString(
            $this->getDataFileContents('no-color-success-yellow.txt'),
            $process->getOutput()
        );

        self::assertStringContainsString('Exit code:', $process->getOutput());
        self::assertStringContainsString('Time:', $process->getOutput());
        self::assertStringContainsString('Memory:', $process->getOutput());

        self::assertSame(0, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testNoCoberturaFileError(): void
    {
        $process = $this->runProcess();

        self::assertStringContainsString(
            'ERROR: Missing required argument: path to cobertura XML file.',
            $process->getOutput()
        );

        self::assertSame(1, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    public function testInitSuccess(): void
    {
        $process = $this->runProcess([
            '--init',
        ]);
        
        unlink(
            (string) realpath((string) getcwd() . '/phpunit-cobertura-formatter.yml.dist')
        );

        self::assertStringContainsString(
            'Default config file created at',
            $process->getOutput()
        );

        self::assertSame(0, $process->getExitCode());
        self::assertEmpty($process->getErrorOutput());
    }

    /**
     * @param string[] $options
     */
    private function runProcess(array $options = []): Process
    {
        $process = new Process([
            'php',
            self::SCRIPT_FILE,
            ...$options
        ]);

        $process->setTimeout(60);
        $process->run();

        return $process;
    }
}
