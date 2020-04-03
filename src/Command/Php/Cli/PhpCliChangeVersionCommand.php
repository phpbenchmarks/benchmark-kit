<?php

declare(strict_types=1);

namespace App\Command\Php\Cli;

use App\{
    Command\AbstractCommand,
    Command\Behavior\PhpVersionArgumentTrait
};
use Symfony\Component\Console\Output\OutputInterface;

final class PhpCliChangeVersionCommand extends AbstractCommand
{
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'php:cli:change-version';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Change PHP CLI version')
            ->addPhpVersionArgument($this);
    }

    protected function doExecute(): int
    {
        $phpVersion = $this->getPhpVersionFromArgument($this->getInput());

        $this
            ->outputTitle('Change PHP CLI version to ' . $phpVersion->toString())
            ->assertPhpVersionArgument($this->getInput())
            ->runProcess(
                ['sudo', '/usr/bin/update-alternatives', '--set', 'php', '/usr/bin/php' . $phpVersion->toString()],
                OutputInterface::VERBOSITY_VERBOSE
            )
            ->outputSuccess('PHP CLI version changed to ' . $phpVersion->toString() . '.');

        return 0;
    }
}
