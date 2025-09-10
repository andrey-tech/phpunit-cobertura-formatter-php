<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Renderer\Colorizer;

use function str_contains;

final class Stats
{
    private const string TAG_RED = 'red';
    private const string TAG_YELLOW = 'yellow';

    /**
     * @var array<string, int>
     */
    private array $tagCounts = [];

    public function update(string $tag): void
    {
        if (!isset($this->tagCounts[$tag])) {
            $this->tagCounts[$tag] = 0;
        }

        $this->tagCounts[$tag]++;
    }

    public function isRed(): bool
    {
        return (bool) $this->redCount();
    }

    public function isYellow(): bool
    {
        return (bool) $this->yellowCount();
    }

    private function redCount(): int
    {
        return $this->tagCount(self::TAG_RED);
    }

    private function yellowCount(): int
    {
        return $this->tagCount(self::TAG_YELLOW);
    }

    private function tagCount(string $tag): int
    {
        $count = 0;
        foreach ($this->tagCounts as $key => $value) {
            if (str_contains($key, $tag)) {
                $count += $value;
            }
        }

        return $count;
    }
}
