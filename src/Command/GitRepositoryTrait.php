<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkType,
    ComponentConfiguration\ComponentConfiguration
};

trait GitRepositoryTrait
{
    protected function getCommonRepositoryName(): string
    {
        return 'phpbenchmarks/' . ComponentConfiguration::getComponentSlug() . '-common';
    }

    protected function getCommonDevBranchName(): string
    {
        return
            'dev-'
            . ComponentConfiguration::getComponentSlug()
            . '_'
            . ComponentConfiguration::getCoreDependencyMajorVersion()
            . '_'
            . BenchmarkType::getSlug(ComponentConfiguration::getBenchmarkType())
            . '_prepare';
    }

    protected function getCommonProdBranchPrefix(): string
    {
        return
            ComponentConfiguration::getCoreDependencyMajorVersion()
            . '.'
            . ComponentConfiguration::getBenchmarkType()
            . '.';
    }
}
