<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser;

final class ClassMetricsCollection
{
    /**
     * @var list<ClassMetrics>
     */
    private array $items = [];

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
}
