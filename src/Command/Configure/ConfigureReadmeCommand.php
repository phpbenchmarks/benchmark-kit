<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ConfigureReadmeCommand extends AbstractConfigureCommand
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
        $this->outputTitle('Creation of README.md');

        $readmePath = $this->getInstallationPath() . '/README.md';
        $copied = copy($this->getDefaultConfigurationPath() . '/README.md', $readmePath);
        if ($copied === false) {
            $this->throwError('Error while copying README.md.');
        }
        $this->outputSuccess('README.md copied.');

        $this
            ->defineStringVariable(
                '____PHPBENCHMARKS_COMPONENT_NAME____',
                ComponentConfiguration::getComponentName(),
                $readmePath
            )
            ->defineStringVariable(
                '____PHPBENCHMARKS_COMPONENT_SLUG____',
                ComponentConfiguration::getComponentSlug(),
                $readmePath
            )
            ->defineStringVariable(
                '____PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION____',
                (string) ComponentConfiguration::getCoreDependencyMajorVersion(),
                $readmePath
            )
            ->defineStringVariable(
                '____PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION____',
                (string) ComponentConfiguration::getCoreDependencyMinorVersion(),
                $readmePath
            );

        return $this;
    }
}
