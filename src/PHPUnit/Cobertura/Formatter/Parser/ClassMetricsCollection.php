<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser;

use Closure;

use function array_filter;
use function array_values;

final class ClassMetricsCollection
{
    /**
     * @param list<ClassMetrics> $items
     */
    public function __construct(
        private array $items = []
    ) {
    }

    public function add(ClassMetrics $classMetrics): void
    {
        $this->items[] = $classMetrics;
    }

    /**
     * @return list<ClassMetrics>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param Closure(ClassMetrics):bool $closure
     */
    public function filter(Closure $closure): self
    {
        return new self(
            array_values(
                array_filter($this->items, $closure)
            )
        );
    }
}
