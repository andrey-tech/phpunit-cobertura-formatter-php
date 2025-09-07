<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\PHPUnit\Cobertura\Formatter\Parser;

use function pow;

final class Crap
{
    /**
     * @see https://www.artima.com/weblogs/viewpost.jsp?thread=210575
     */
    public function calculate(int $complexity, float $coverage): int
    {
        $crap = (float) pow($complexity, 2) * pow(1.0 - $coverage, 3) + (float) $complexity;

        return (int) $crap;
    }
}
