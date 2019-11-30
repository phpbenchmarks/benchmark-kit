<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ConfigurePhpBenchmarksPhpVersionCompatibleCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:phpVersionCompatible';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create configurations for each compatible PHP version');
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle('Configuration of PHP compatibles versions');

        foreach (PhpVersion::getAll() as $phpVersion) {
            $phpConfigurationPath = Path::getPhpConfigurationPath($phpVersion);

            if ($this->askConfirmationQuestion('Is PHP ' . $phpVersion->toString() . ' compatible?')) {
                $this
                    ->createDirectory($phpConfigurationPath)
                    ->filePutContent($phpConfigurationPath . '/php.ini', '');
            } else {
                $this->removeDirectory($phpConfigurationPath);
            }
        }

        return $this;
    }
}
