<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Utils\Path
};

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
            ->removeDirectory(Path::getBenchmarkConfigurationPath() . '/.circleci')
            ->writeFileFromTemplate('.circleci/config.yml');
    }
}
