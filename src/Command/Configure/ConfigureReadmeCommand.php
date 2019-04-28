<?php

declare(strict_types=1);

namespace App\Command\Configure;

use AbstractComponentConfiguration\AbstractComponentConfiguration;
use App\{
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

class ConfigureReadmeCommand extends AbstractConfigureCommand
{
    use DefineVariableTrait;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:readme')
            ->setDescription('Create README.md');
    }

    protected function doExecute(): AbstractCommand
    {
        $this->title('Creation of README.md');

        $readmePath = $this->getInstallationPath() . '/README.md';
        $copied = copy($this->getDefaultConfigurationPath() . '/README.md', $readmePath);
        if ($copied === false) {
            $this->error('Error while copying README.md.');
        }
        $this->success('README.md copied.');

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
