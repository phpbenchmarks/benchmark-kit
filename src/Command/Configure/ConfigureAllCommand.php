<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

class ConfigureAllCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('configure:all')
            ->setDescription('Call all configure commands');
    }

    protected function doExecute(): parent
    {
        return $this
            ->runCommand('configure:directory')
            ->runCommand('configure:component')
            ->warningSourceCodeUrls()
            ->runCommand('configure:component:sourceCodeUrls', ['--skip-component-creation' => true])
            ->runCommand('configure:initBenchmark')
            ->runCommand('configure:vhost')
            ->runCommand('configure:responseBody');
    }

    protected function warningSourceCodeUrls(): self
    {
        if ($this->skipSourceCodeUrls() === false) {
            $this->warning('You can skip source code urls configuration with --skip-source-code-urls parameter.', false);
        }

        return $this;
    }
}
