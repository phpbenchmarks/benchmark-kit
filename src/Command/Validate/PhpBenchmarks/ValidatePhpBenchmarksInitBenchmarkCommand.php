<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksInitBenchmarkCommand
};

final class ValidatePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . $this->getInitBenchmarkFilePath(true));
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle('Validation of ' . $this->getInitBenchmarkFilePath(true))
            ->assertFileExist(
                $this->getInitBenchmarkFilePath(),
                ConfigurePhpBenchmarksInitBenchmarkCommand::getDefaultName()
            );
    }
}
