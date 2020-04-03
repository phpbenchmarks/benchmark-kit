<?php

declare(strict_types=1);

namespace App\BenchmarkConfiguration;

use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class BenchmarkConfigurationArray extends ObjectArray
{
    public function __construct(...$benchmarkConfigurations)
    {
        parent::__construct($benchmarkConfigurations, BenchmarkConfiguration::class);
    }

    public function current(): ?BenchmarkConfiguration
    {
        return parent::current();
    }

    public function offsetGet($offset): BenchmarkConfiguration
    {
        return parent::offsetGet($offset);
    }
}
