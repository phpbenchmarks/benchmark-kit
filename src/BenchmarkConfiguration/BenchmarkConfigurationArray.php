<?php

declare(strict_types=1);

namespace App\BenchmarkConfiguration;

use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class BenchmarkConfigurationArray extends ObjectArray
{
    /** @param iterable<BenchmarkConfiguration> $benchmarkConfigurations */
    public function __construct(iterable $benchmarkConfigurations = [])
    {
        parent::__construct($benchmarkConfigurations, BenchmarkConfiguration::class);
    }

    public function current(): ?BenchmarkConfiguration
    {
        return parent::current();
    }

    /** @param mixed $offset */
    public function offsetGet($offset): BenchmarkConfiguration
    {
        return parent::offsetGet($offset);
    }
}
