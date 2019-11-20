<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

final class ConfigureCircleCiCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:circleci';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure CircleCI');
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle('Configure CircleCI')
            ->removeDirectory($this->getInstallationPath() . '/.circleci')
            ->writeFileFromTemplate('.circleci/config.yml');
    }
}
