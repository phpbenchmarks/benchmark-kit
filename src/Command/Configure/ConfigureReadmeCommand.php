<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark
};

final class ConfigureReadmeCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:readme';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create README.md');
    }

    protected function doExecute(): AbstractCommand
    {
        return $this
            ->outputTitle('Creation of README.md')
            ->writeFileFromTemplate(
                'README.md',
                [
                    'componentName' => Benchmark::getComponentName(),
                    'componentSlug' => Benchmark::getComponentSlug(),
                    'coreDependencyMajorVersion' => (string) Benchmark::getCoreDependencyMajorVersion(),
                    'coreDependencyMinorVersion' => (string) Benchmark::getCoreDependencyMinorVersion()
                ]
            );
    }
}
