<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
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

    protected function doExecute(): int
    {
        $this->outputTitle('Validate entrypoint');

        $entryPointFilePath = Path::getSourceCodePath() . '/' . Benchmark::getSourceCodeEntryPoint();
        if (is_readable($entryPointFilePath) === false) {
            throw new \Exception(
                'Entry point ' . Benchmark::getSourceCodeEntryPoint() . 'does not exists or is not readable.'
            );
        }

        $this->outputSuccess(Path::rmPrefix($entryPointFilePath) . ' is readable.');

        return 0;
    }
}
