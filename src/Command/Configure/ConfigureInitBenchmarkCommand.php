<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

final class ConfigureInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getInitBenchmarkFilePath(true));
    }

    protected function doExecute(): AbstractCommand
    {
        $initBenchmarkRelativePath = $this->getInitBenchmarkFilePath(true);

        return $this
            ->outputTitle('Creation of ' . $initBenchmarkRelativePath)
            ->writeFileFromTemplate($initBenchmarkRelativePath)
            ->outputWarning(
                'Default initBenchmark.sh (called to initialize your benchmark) has been created. Feel free to edit it.'
            )
            ->runProcess(['chmod', '+x', $this->getInitBenchmarkFilePath()])
            ->outputSuccess('Make ' . $initBenchmarkRelativePath . ' executable.');
    }
}
