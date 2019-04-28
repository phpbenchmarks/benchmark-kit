<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\Command\AbstractCommand;

class ConfigureInitBenchmarkCommand extends AbstractConfigureCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:initBenchmark')
            ->setDescription('Create .phpbenchmarks/initBenchmark.sh');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->title('Creation of .phpbenchmarks/initBenchmark.sh')
            ->copyDefaultConfigurationFile(
                'initBenchmark.sh',
                false,
                'File has been created but is very basic. Don\'t forget to edit it.'
            )
            ->exec('chmod +x /var/www/phpbenchmarks/.phpbenchmarks/initBenchmark.sh')
            ->success('Make .phpbenchmarks/initBenchmark.sh executable.')
            ->runCommand('validate:configuration:initBenchmark');

        return $this;
    }
}
