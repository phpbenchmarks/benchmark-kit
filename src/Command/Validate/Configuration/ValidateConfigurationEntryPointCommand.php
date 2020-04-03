<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Utils\Path
};

final class ValidateConfigurationEntryPointCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:entry-point';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate entry point file');
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validate entry point');

        $entryPointFilePath = Path::getSourceCodePath() . '/' . Benchmark::getSourceCodeEntryPoint();
        if (is_readable($entryPointFilePath) === false) {
            throw new \Exception(
                'Entry point "' . Benchmark::getSourceCodeEntryPoint() . '" does not exists or is not readable.'
            );
        }

        $this->outputSuccess(Path::rmPrefix($entryPointFilePath) . ' is readable.');

        return 0;
    }
}
