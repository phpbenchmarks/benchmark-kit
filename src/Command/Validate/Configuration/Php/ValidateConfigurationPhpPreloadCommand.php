<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration\Php;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Command\Configure\Php\ConfigurePhpPreloadCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidateConfigurationPhpPreloadCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:php:preload';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getPreloadPath(new PhpVersion(0, 0))));
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validation of ' . Path::rmPrefix(Path::getPreloadPath(new PhpVersion(0, 0))));

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $preloadPath = Path::getPreloadPath($phpVersion);

            if ($phpVersion->isPreloadAvailable() === true) {
                $this->assertFileExist($preloadPath, ConfigurePhpPreloadCommand::getDefaultName());
            } else {
                $this->assertFileNotExists($preloadPath);
            }
        }

        return 0;
    }
}
