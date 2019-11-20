<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ConfigureReadmeCommand extends AbstractCommand
{
    use DefineVariableTrait;

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
                    'componentName' => ComponentConfiguration::getComponentName(),
                    'componentSlug' => ComponentConfiguration::getComponentSlug(),
                    'coreDependencyMajorVersion' => (string) ComponentConfiguration::getCoreDependencyMajorVersion(),
                    'coreDependencyMinorVersion' => (string) ComponentConfiguration::getCoreDependencyMinorVersion()
                ]
            );
    }
}
