<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration\Php;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidateConfigurationPhpIniCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:php:ini';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getPhpIniPath(new PhpVersion(0, 0))));
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validation of ' . Path::rmPrefix(Path::getPhpIniPath(new PhpVersion(0, 0))));

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $iniPath = Path::getPhpIniPath($phpVersion);
            if (is_readable($iniPath)) {
                $this->outputSuccess(Path::rmPrefix($iniPath) . ' exists and is readable.');
            } else {
                throw new \Exception(Path::rmPrefix($iniPath) . ' does not exists or is not readable.');
            }
        }

        return 0;
    }
}
