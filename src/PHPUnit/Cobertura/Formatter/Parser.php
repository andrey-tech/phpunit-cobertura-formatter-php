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
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\Crap;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\File;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\MethodMetrics;
use AndreyTech\PHPUnit\Cobertura\Formatter\Parser\MethodMetricsCollection;
use Exception;
use SimpleXMLElement;

final readonly class Parser
{
    private Crap $crap;

    public function __construct()
    {
        $this->crap = new Crap();
    }

    /**
     * @throws Exception
     */
    public function parse(File $file): ClassMetricsCollection
    {
        $xml = new SimpleXMLElement($file->contents());

        $classMetricsCollection = new ClassMetricsCollection();
        foreach ($xml->packages->package ?? [] as $package) {
            foreach ($package->classes->class ?? [] as $class) {

                $methodMetricsCollection = new MethodMetricsCollection();
                foreach ($class->methods->method ?? [] as $method) {
                    $methodMetricsCollection->add(
                        $this->parseMethodMetrics($method)
                    );
                }

                $classMetricsCollection->add(
                    $this->parseClassMetrics($class, $methodMetricsCollection)
                );
            }
        }

        return $classMetricsCollection;
    }

    private function parseMethodMetrics(SimpleXMLElement $method): MethodMetrics
    {
        return new MethodMetrics(
            name: $this->getStringAttribute($method, 'name'),
            lineRate: $this->getFloatAttribute($method, 'line-rate'),
            branchRate: $this->getFloatAttribute($method, 'branch-rate'),
            complexity: $this->getIntegerAttribute($method, 'complexity'),
            crap: $this->calculateCrap($method)
        );
    }

    private function parseClassMetrics(
        SimpleXMLElement $class,
        MethodMetricsCollection $methodMetricsCollection
    ): ClassMetrics {
        return new ClassMetrics(
            name: $this->getStringAttribute($class, 'name'),
            lineRate: $this->getFloatAttribute($class, 'line-rate'),
            branchRate: $this->getFloatAttribute($class, 'branch-rate'),
            complexity: $this->getIntegerAttribute($class, 'complexity'),
            crap: $this->calculateCrap($class),
            methodMetricsCollection: $methodMetricsCollection
        );
    }

    private function calculateCrap(SimpleXMLElement $xml): int
    {
        return $this->crap->calculate(
            $this->getIntegerAttribute($xml, 'complexity'),
            $this->getFloatAttribute($xml, 'line-rate')
        );
    }

    private function getStringAttribute(SimpleXMLElement $xml, string $attribute): string
    {
        return (string) ($xml[$attribute] ?? '');
    }

    private function getFloatAttribute(SimpleXMLElement $xml, string $attribute): float
    {
        return (float) ($xml[$attribute] ?? 0.0);
    }

    private function getIntegerAttribute(SimpleXMLElement $xml, string $attribute): int
    {
        return (int) ($xml[$attribute] ?? 0);
    }
}
