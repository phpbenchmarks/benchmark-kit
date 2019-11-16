<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

final class ConfigureDirectoryCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:directory';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getConfigurationPath(true) . ' directory and subdirectories');
    }

    protected function doExecute(): parent
    {
        if (
            is_dir($this->getConfigurationPath()) === false
            || is_dir($this->getResponseBodyPath()) === false
            || is_dir($this->getComposerPath()) === false
        ) {
            $this->outputTitle('Creation of ' . $this->getConfigurationPath(true) . ' directory and subdirectories');

            $this
                ->createDirectory($this->getConfigurationPath())
                ->createDirectory($this->getResponseBodyPath())
                ->createDirectory($this->getComposerPath());
        }

        return $this;
    }

    private function createDirectory(string $directory): self
    {
        if (is_dir($directory) === false) {
            $created = mkdir($directory);
            if ($created === false) {
                $this->throwError('Cannot create ' . $directory . ' directory.');
            }
            $this->outputSuccess($directory . ' directory created.');
        }

        return $this;
    }
}
