<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureGitignoreCommand,
    Utils\Path
};

final class ValidateConfigurationGitignoreCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:gitignore';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate .gitignore');
    }

    protected function onError(): AbstractCommand
    {
        return $this->outputCallPhpbenchkitWarning(ConfigureGitignoreCommand::getDefaultName());
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validate .gitignore');

        $gitIgnoreFileName = Path::getSourceCodePath() . '/.gitignore';

        if (is_readable($gitIgnoreFileName) === false) {
            throw new \Exception('.gitignore file not found.');
        }

        if (strpos(file_get_contents($gitIgnoreFileName), ConfigureGitignoreCommand::IGNORE_COMPOSER_LOCK) === false) {
            throw new \Exception('.gitignore should contains ' . ConfigureGitignoreCommand::IGNORE_COMPOSER_LOCK . '.');
        }

        $this->outputSuccess('.gitignore contains "' . ConfigureGitignoreCommand::IGNORE_COMPOSER_LOCK . '".');

        return 0;
    }
}
