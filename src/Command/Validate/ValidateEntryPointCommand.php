<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureEntryPointCommand,
    Benchmark\Benchmark,
    Utils\Path
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

        $entryPointRelativeFilePath = Benchmark::getBenchmarkEntryPoint();
        $entryPointFilePath = Path::getBenchmarkPath() . '/' . $entryPointRelativeFilePath;

        if (is_readable($entryPointFilePath) === false) {
            throw new \Exception(
                'Entrypoint ' . $entryPointRelativeFilePath . ' is not readable.'
            );
        }

        $content = file_get_contents($entryPointFilePath);

        if (strpos($content, ConfigureEntryPointCommand::STATS_COMMENT) === false) {
            throw new \Exception(
                Path::rmPrefix($entryPointFilePath)
                    . ' should contains "'
                    . ConfigureEntryPointCommand::STATS_COMMENT
                    . '" at the end of the file.'
            );
        }

        return $this->outputSuccess(
            Path::rmPrefix($entryPointFilePath)
                . ' contains "'
                . ConfigureEntryPointCommand::STATS_COMMENT
                . '".'
        );
    }
}
