<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksInitBenchmarkCommand,
    Utils\Path
};

final class ValidatePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::removeBenchmarkPathPrefix(Path::getInitBenchmarkPath()));
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle('Validation of ' . Path::removeBenchmarkPathPrefix(Path::getInitBenchmarkPath()))
            ->assertFileExist(
                Path::getInitBenchmarkPath(),
                ConfigurePhpBenchmarksInitBenchmarkCommand::getDefaultName()
            );
    }
}
