<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureGitignoreCommand,
    Utils\Path
};

final class ValidateGitignoreCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:gitignore';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate .gitignore');
    }

    protected function onError(): AbstractCommand
    {
        return $this->outputCallPhpbenchkitWarning(ConfigureGitignoreCommand::getDefaultName());
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle('Validate .gitignore');

        $gitIgnoreFileName = Path::getBenchmarkConfigurationPath() . '/.gitignore';

        if (is_readable($gitIgnoreFileName) === false) {
            $this->throwError('.gitignore file not found.');
        }

        if (strpos(file_get_contents($gitIgnoreFileName), ConfigureGitignoreCommand::IGNORE_COMPOSER_LOCK) === false) {
            $this->throwError('.gitignore should contains ' . ConfigureGitignoreCommand::IGNORE_COMPOSER_LOCK . '.');
        }

        return $this->outputSuccess('.gitignore contains "' . ConfigureGitignoreCommand::IGNORE_COMPOSER_LOCK . '".');
    }
}
