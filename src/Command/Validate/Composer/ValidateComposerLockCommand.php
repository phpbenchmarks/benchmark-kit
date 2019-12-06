<?php

declare(strict_types=1);

namespace App\Command\Validate\Composer;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Utils\Path
};

final class ValidateComposerLockCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:composer:lock';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate composer.lock does not exists');
    }

    protected function onError(): AbstractCommand
    {
        return $this->outputWarning(
            'You can use "phpbenchkit '
                . ComposerUpdateCommand::getDefaultName()
                . '" to update your dependencies for each compatible PHP version.'
        );
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle('Validation of composer.lock');

        if (file_exists(Path::getBenchmarkPath() . '/composer.lock')) {
            throw new \Exception('composer.lock shoud not exists.');
        }

        return $this->outputSuccess('composer.lock does not exists.');
    }
}
