<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Renderer;

use AndreyTech\PHPUnit\Cobertura\Formatter\Config\File as Config;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\ClassMetrics;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\MethodMetrics;
use AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer\Stats;

use function array_shift;
use function explode;
use function implode;
use function sprintf;

use const PHP_INT_MAX;

final readonly class Colorizer
{
    public function __construct(
        private Stats $stats,
        private Config $config
    ) {
    }

    public function classLineCoverage(ClassMetrics $classMetrics): string
    {
        return $this->colorize(
            100.0 * $classMetrics->lineRate,
            $this->ranges('colorizer.metrics.class.lcov'),
            '%.02f'
        );
    }

    public function classBranchCoverage(ClassMetrics $classMetrics): string
    {
        return $this->colorize(
            100.0 * $classMetrics->branchRate,
            $this->ranges('colorizer.metrics.class.bcov'),
            '%.02f'
        );
    }

    public function classComplexity(ClassMetrics $classMetrics): string
    {
        return $this->colorize(
            $classMetrics->complexity,
            $this->ranges('colorizer.metrics.class.ccn'),
            '%u'
        );
    }

    public function classCrap(ClassMetrics $classMetrics): string
    {
        return $this->colorize(
            $classMetrics->crap,
            $this->ranges('colorizer.metrics.class.crap'),
            '%u'
        );
    }

    public function methodName(MethodMetrics $methodMetrics): string
    {
        return $methodMetrics->name;
    }

    public function methodLineCoverage(MethodMetrics $methodMetrics): string
    {
        return $this->colorize(
            100.0 * $methodMetrics->lineRate,
            $this->ranges('colorizer.metrics.method.lcov'),
            '%.02f'
        );
    }

    public function methodBranchCoverage(MethodMetrics $methodMetrics): string
    {
        return $this->colorize(
            100.0 * $methodMetrics->branchRate,
            $this->ranges('colorizer.metrics.method.bcov'),
            '%.02f'
        );
    }

    public function methodComplexity(MethodMetrics $methodMetrics): string
    {
        return $this->colorize(
            $methodMetrics->complexity,
            $this->ranges('colorizer.metrics.method.ccn'),
            '%u'
        );
    }

    public function methodCrap(MethodMetrics $methodMetrics): string
    {
        return $this->colorize(
            $methodMetrics->crap,
            $this->ranges('colorizer.metrics.method.crap'),
            '%u'
        );
    }

    /**
     * @param array<string, array{0: int|float|null, 1?: int|float|null}>|null $ranges
     */
    private function colorize(int|float $value, ?array $ranges, string $format): string
    {
        if (null === $ranges) {
            return sprintf($format, $value);
        }

        foreach ($ranges as $decor => $range) {
            $minValue = $range[0] ?? -PHP_INT_MAX;
            $maxValue = $range[1] ?? PHP_INT_MAX;

            if ($value >= $minValue && $value <= $maxValue) {
                return $this->renderTemplate($value, $decor, $format);
            }
        }

        return sprintf($format, $value);
    }

    private function renderTemplate(int|float $value, string $decor, string $format): string
    {
        $this->stats->update($decor);

        $decorElements = explode('+', $decor);
        $tags = [sprintf('fg=%s', array_shift($decorElements))];

        $options = array_shift($decorElements);
        if (null !== $options) {
            $tags[] = sprintf('options=%s', $options);
        }

        return sprintf(
            '<%s>%s</>',
            implode(';', $tags),
            sprintf($format, $value)
        );
    }

    /**
     * @return array<string, array{0: int|float|null, 1?: int|float|null}>|null
     */
    private function ranges(string $key): ?array
    {
        /** @var array<string, array{0: int|float|null, 1?: int|float|null}>|null $ranges */
        $ranges = $this->config->get($key);

        return $ranges;
    }
}
