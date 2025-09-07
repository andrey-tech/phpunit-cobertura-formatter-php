<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser;

final class MethodMetricsCollection
{
    /**
     * @var list<MethodMetrics>
     */
    private array $items = [];

    public function add(MethodMetrics $methodMetrics): void
    {
        $this->items[] = $methodMetrics;
    }

    /**
     * @return list<MethodMetrics>
     */
    public function all(): array
    {
        return $this->items;
    }
}
