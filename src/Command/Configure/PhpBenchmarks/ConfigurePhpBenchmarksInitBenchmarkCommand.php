<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Utils\Path
};

final class ConfigurePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . Path::removeBenchmarkPathPrefix(Path::getInitBenchmarkPath()));
    }

    protected function doExecute(): AbstractCommand
    {
        $initBenchmarkPath = Path::getInitBenchmarkPath();
        $initBenchmarkRelativePath = Path::removeBenchmarkPathPrefix($initBenchmarkPath);

        return $this
            ->outputTitle('Creation of ' . $initBenchmarkRelativePath)
            ->writeFileFromTemplate($initBenchmarkRelativePath)
            ->outputWarning(
                'Default initBenchmark.sh (called to initialize your benchmark) has been created. Feel free to edit it.'
            )
            ->runProcess(['chmod', '+x', $initBenchmarkPath])
            ->outputSuccess('Make ' . $initBenchmarkRelativePath . ' executable.');
    }
}
