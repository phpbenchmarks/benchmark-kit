<?php

declare(strict_types=1);

namespace App\Command\Composer;

use App\{
    Command\AbstractCommand,
    Command\Php\Cli\PhpCliChangeVersionCommand,
    Benchmark\Benchmark,
    Command\Validate\Configuration\Composer\ValidateConfigurationComposerJsonCommand,
    Utils\Path
};
use Symfony\Component\Console\Output\OutputInterface;

final class ComposerUpdateCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'composer:update';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Execute composer update for all configured PHP versions');
    }

    protected function doExecute(): int
    {
        $this->runCommand(ValidateConfigurationComposerJsonCommand::getDefaultName());

        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $composerLockFilePath = Path::getComposerLockPath($phpVersion);

            $this
                ->runCommand(PhpCliChangeVersionCommand::getDefaultName(), ['phpVersion' => $phpVersion->toString()])
                ->outputTitle('Update Composer dependencies')
                ->runProcess(['composer', 'update', '--ansi'], OutputInterface::VERBOSITY_VERBOSE)
                ->outputSuccess('Composer update done.')
                ->runProcess(['mv', 'composer.lock', $composerLockFilePath])
                ->outputSuccess('Move composer.lock to ' . Path::rmPrefix($composerLockFilePath) . '.');
        }

        return 0;
    }
}
