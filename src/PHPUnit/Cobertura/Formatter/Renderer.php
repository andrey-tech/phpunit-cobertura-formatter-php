<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter;

use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\ClassMetrics;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\ClassMetricsCollection;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\MethodMetrics;
use AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final readonly class Renderer
{
    public function __construct(
        private OutputInterface $output,
        private Colorizer $colorizer
    ) {
    }

    public function render(ClassMetricsCollection $classMetricsCollection): void
    {
        foreach ($classMetricsCollection->all() as $classMetrics) {
            $this->renderTable($classMetrics);
        }
    }

    private function renderTable(ClassMetrics $classMetrics): void
    {
        $this->renderTableTitle($classMetrics);

        $table = $this->buildTable();
        $table = $this->addTableHeader($table);
        $this->addTableClassRow($table, $classMetrics);

        foreach ($classMetrics->methodMetricsCollection->all() as $methodMetrics) {
            $this->addTableMethodRow($table, $methodMetrics);
        }

        $table->render();
        $this->addTableSeparator();
    }

    private function renderTableTitle(ClassMetrics $classMetrics): void
    {
        $this->output->writeln(
            sprintf('<fg=yellow>CLASS: %s</>', $classMetrics->name)
        );
    }

    private function buildTable(): Table
    {
        $style = new TableStyle();
        $style->setCellHeaderFormat('<fg=white;bg=default;options=bold>%s</>');

        $table = new Table($this->output);
        $table->setStyle($style);

        return $table;
    }

    private function addTableHeader(Table $table): Table
    {
        $table->setHeaders(['METHOD', 'lcov', 'bcov', 'ccn', 'crap']);
        $table->setColumnWidths([25]);
        $table->setColumnMaxWidth(0, 50);

        return $table;
    }

    private function addTableClassRow(Table $table, ClassMetrics $classMetrics): void
    {
        $table->addRow([
            '<fg=white;bg=default;options=bold>CLASS</>',
            $this->colorizer->classLineCoverage($classMetrics),
            $this->colorizer->classBranchCoverage($classMetrics),
            $this->colorizer->classComplexity($classMetrics),
            $this->colorizer->classCrap($classMetrics),
        ]);
    }

    private function addTableMethodRow(Table $table, MethodMetrics $methodMetrics): void
    {
        $table->addRow([
            $this->colorizer->methodName($methodMetrics),
            $this->colorizer->methodLineCoverage($methodMetrics),
            $this->colorizer->methodBranchCoverage($methodMetrics),
            $this->colorizer->methodComplexity($methodMetrics),
            $this->colorizer->methodCrap($methodMetrics),
        ]);
    }

    private function addTableSeparator(): void
    {
        $this->output->writeln('');
    }
}
