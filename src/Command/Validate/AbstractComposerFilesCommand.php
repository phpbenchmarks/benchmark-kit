<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

abstract class AbstractComposerFilesCommand extends AbstractCommand
{
    protected function getCommonRepositoryName()
    {
        return 'phpbenchmarks/' . ComponentConfiguration::getComponentSlug() . '-common';
    }

    protected function getCommonDevBranchName()
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

    protected function getCommonProdBranchPrefix()
    {
        return
            ComponentConfiguration::getCoreDependencyMajorVersion()
            . '.'
            . ComponentConfiguration::getBenchmarkType()
            . '.';
    }
}
