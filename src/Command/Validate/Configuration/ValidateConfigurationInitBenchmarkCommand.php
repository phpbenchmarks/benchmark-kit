<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Command\Configure\ConfigureInitBenchmarkCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidateConfigurationInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:init-benchmark';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getInitBenchmarkPath(new PhpVersion(0, 0))));
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validation of ' . Path::rmPrefix(Path::getInitBenchmarkPath(new PhpVersion(0, 0))));

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $this->assertFileExist(
                Path::getInitBenchmarkPath($phpVersion),
                ConfigureInitBenchmarkCommand::getDefaultName()
            );
        }

        return 0;
    }
}
