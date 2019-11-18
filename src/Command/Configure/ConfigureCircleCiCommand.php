<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Command\CreateDirectoryTrait,
    Command\FileContentTrait
};

final class ConfigureCircleCiCommand extends AbstractCommand
{
    use CreateDirectoryTrait;
    use FileContentTrait;

    /** @var string */
    protected static $defaultName = 'configure:circle-ci';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure CircleCI');
    }

    protected function doExecute(): parent
    {
        $circleCiDirectory = $this->getInstallationPath() . '/.circleci';

        return $this
            ->outputTitle('Configure CircleCI')
            ->createDirectory($circleCiDirectory, $this)
            ->filePutContent($circleCiDirectory . '/config.yml', $this->getCircleCiConfigContent(), $this);
    }

    protected function getCircleCiConfigContent(): string
    {
        return $this->fileGetContent(
            __DIR__ . '/../../../templates/CircleCi/config.yml'
        );
    }
}
