<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Validate\ValidateConfigurationInitBenchmarkCommand
};

final class ConfigureInitBenchmarkCommand extends AbstractConfigureCommand
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
        $this
            ->outputTitle('Creation of ' . $this->getInitBenchmarkFilePath(true))
            ->copyDefaultConfigurationFile(
                'initBenchmark.sh',
                false,
                'File has been created but is very basic. Don\'t forget to edit it.'
            )
            ->exec('chmod +x ' . $this->getInitBenchmarkFilePath())
            ->outputSuccess('Make ' . $this->getInitBenchmarkFilePath(true) . ' executable.')
            ->runCommand(ValidateConfigurationInitBenchmarkCommand::getDefaultName());

        return $this;
    }
}
