<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureEntryPointCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ValidateEntryPointCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:entrypoint';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate entrypoint');
    }

    protected function onError(): parent
    {
        return $this->outputCallPhpbenchkitWarning(ConfigureEntryPointCommand::getDefaultName());
    }

    protected function doExecute(): parent
    {
        $this->outputTitle('Validate entrypoint');

        $entryPointRelativeFileName = ComponentConfiguration::getEntryPointFileName();
        $entryPointFileName = $this->getInstallationPath() . '/' . $entryPointRelativeFileName;

        if (is_readable($entryPointFileName) === false) {
            throw new \Exception(
                'Entrypoint ' . $entryPointRelativeFileName . ' is not readable.'
            );
        }

        $content = file_get_contents($entryPointFileName);

        if (strpos($content, ConfigureEntryPointCommand::STATS_COMMENT) === false) {
            $this->throwError(
                'Entrypoint '
                    . $entryPointFileName
                    . ' should contains "'
                    . ConfigureEntryPointCommand::STATS_COMMENT
                    . '" at the end of the file.'
            );
        }

        return $this->outputSuccess(
            'Entrypoint '
                . $entryPointFileName
                . ' contains "'
                . ConfigureEntryPointCommand::STATS_COMMENT
                . '".'
        );
    }
}
