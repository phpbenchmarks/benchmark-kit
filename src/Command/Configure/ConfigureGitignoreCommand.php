<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    Utils\Path
};

final class ConfigureGitignoreCommand extends AbstractCommand
{
    public const IGNORE_COMPOSER_LOCK = '/composer.lock';

    /** @var string */
    protected static $defaultName = 'configure:gitignore';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure .gitignore');
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Configure .gitignore');

        $gitIgnoreFileName = Path::getSourceCodePath() . '/.gitignore';
        $ignores = (is_readable($gitIgnoreFileName)) ? file_get_contents($gitIgnoreFileName) : '';

        if (strpos($ignores, static::IGNORE_COMPOSER_LOCK) === false) {
            $this->filePutContent($gitIgnoreFileName, static::IGNORE_COMPOSER_LOCK . "\n" . $ignores);
        }

        return 0;
    }
}
