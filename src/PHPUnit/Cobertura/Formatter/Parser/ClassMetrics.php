<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser;

final readonly class ClassMetrics
{
    public function __construct(
        public string $name,
        public float $lineRate,
        public float $branchRate,
        public int $complexity,
        public int $crap,
        public MethodMetricsCollection $methodMetricsCollection
    ) {
    }
}
