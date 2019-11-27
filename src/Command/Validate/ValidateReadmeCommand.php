<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{Command\AbstractCommand,
    Command\Configure\ConfigureReadmeCommand,
    ComponentConfiguration\ComponentConfiguration};

final class ValidateReadmeCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:readme';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate README.md');
    }

    protected function onError(): parent
    {
        return $this->outputCallPhpbenchkitWarning(ConfigureReadmeCommand::getDefaultName());
    }

    protected function doExecute(): parent
    {
        $this->outputTitle('Validation of README.md');

        $readmeFileName = $this->getInstallationPath() . '/README.md';
        if (is_readable($readmeFileName) === false) {
            $this->throwError('README.md does not exists or is not readable.');
        }
        $content = file_get_contents($readmeFileName);

        $expectedContent = $this->renderTemplate(
            'README.md',
            [
                'componentName' => ComponentConfiguration::getComponentName(),
                'componentSlug' => ComponentConfiguration::getComponentSlug(),
                'coreDependencyMajorVersion' => (string) ComponentConfiguration::getCoreDependencyMajorVersion(),
                'coreDependencyMinorVersion' => (string) ComponentConfiguration::getCoreDependencyMinorVersion()
            ]
        );

        if ($expectedContent !== $content) {
            $this->throwError('README.md content is not valid.');
        }

        $this->outputSuccess('README.md content is valid.');

        return $this;
    }
}
