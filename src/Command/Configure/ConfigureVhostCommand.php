<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\Validate\ValidateConfigurationVhostCommand
};

final class ConfigureVhostCommand extends AbstractConfigureCommand
{
    /** @var string */
    protected static $defaultName = 'configure:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getVhostFilePath(true));
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->outputTitle('Creation of ' . $this->getVhostFilePath(true))
            ->copyDefaultConfigurationFile('vhost.conf')
            ->runCommand(ValidateConfigurationVhostCommand::getDefaultName());

        return $this;
    }
}
