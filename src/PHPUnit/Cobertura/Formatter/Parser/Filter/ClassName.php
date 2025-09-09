<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser\Filter;

use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\ClassMetrics;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\ClassMetricsCollection;

use function preg_match;
use function sprintf;

final readonly class ClassName
{
    public function __construct(
        private ?string $classNameRegex
    ) {
    }

    public function filter(ClassMetricsCollection $classMetricsCollection): ClassMetricsCollection
    {
        $regex = $this->classNameRegex;

        if (null === $regex || '' === $regex) {
            return $classMetricsCollection;
        }

        return $classMetricsCollection->filter(
            static fn (ClassMetrics $classMetrics): bool => (bool) preg_match(
                sprintf('|%s|', $regex),
                $classMetrics->name
            )
        );
    }
}
