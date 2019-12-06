<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Utils\Path
};

final class ValidatePhpBenchmarksPhpIniCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:phpIni';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate php.ini files');
    }

    protected function doExecute(): parent
    {
        $this->outputTitle('Validation of php.ini');
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $iniPath = Path::getPhpIniPath($phpVersion);
            if (is_readable(Path::getPhpIniPath($phpVersion))) {
                $this->outputSuccess(Path::rmPrefix($iniPath) . ' exists and is readable.');
            } else {
                throw new \Exception(Path::rmPrefix($iniPath) . ' does not exists or is not readable.');
            }
        }

        return $this;
    }
}
