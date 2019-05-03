<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

class ConfigureDirectoryCommand extends AbstractCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:directory')
            ->setDescription('Create ' . $this->getConfigurationPath(true) . ' directory and subdirectories');
    }

    protected function doExecute(): parent
    {
        if (
            is_dir($this->getConfigurationPath()) === false
            || is_dir($this->getResponseBodyPath()) === false
            || is_dir($this->getComposerPath()) === false
        ) {
            $this->title('Creation of ' . $this->getConfigurationPath(true) . ' directory and subdirectories');

            $this
                ->createDirectory($this->getConfigurationPath())
                ->createDirectory($this->getResponseBodyPath())
                ->createDirectory($this->getComposerPath());
        }

        return $this;
    }

    protected function createDirectory(string $directory): self
    {
        if (is_dir($directory) === false) {
            $created = mkdir($directory);
            if ($created === false) {
                $this->error('Cannot create ' . $directory . ' directory.');
            }
            $this->success($directory . ' directory created.');
        }

        return $this;
    }
}
