<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

class ConfigureDirectoryCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('configure:directory')
            ->setDescription('Create .phpbenchmarks and .phpbenchmarks/responseBody directories');
    }

    protected function doExecute(): parent
    {
        if (is_dir($this->getConfigurationPath()) === false || is_dir($this->getResponseBodyPath()) === false) {
            $this->title('Creation of .phpbenchmarks directory');

            if (is_dir($this->getConfigurationPath()) === false) {
                $created = mkdir($this->getConfigurationPath());
                if ($created === false) {
                    $this->error('Cannot create .phpbenchmarks directory.');
                }
                $this->success('.phpbenchmarks directory created.');
            }

            if (is_dir($this->getResponseBodyPath()) === false) {
                $created = mkdir($this->getResponseBodyPath());
                if ($created === false) {
                    $this->error('Cannot create .phpbenchmarks/responseBody directory.');
                }
                $this->success('.phpbenchmarks/responseBody directory created.');
            }
        }

        return $this;
    }
}
