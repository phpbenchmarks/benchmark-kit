<?php

declare(strict_types=1);

namespace App\Command\PhpVersion;

use App\{
    Command\AbstractCommand,
    Command\PhpVersionArgumentTrait
};
use Symfony\Component\Console\Output\OutputInterface;

final class PhpVersionCliDefineCommand extends AbstractCommand
{
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'phpVersion:cli:define';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Define PHP CLI version')
            ->addPhpVersionArgument($this);
    }

    protected function doExecute(): parent
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);

        $this
            ->outputTitle('Define PHP CLI version to ' . $phpVersion->toString())
            ->assertPhpVersionArgument($this)
            ->runProcess(
                ['sudo', '/usr/bin/update-alternatives', '--set', 'php', '/usr/bin/php' . $phpVersion->toString()],
                OutputInterface::VERBOSITY_VERBOSE
            )
            ->outputSuccess('PHP CLI defined to ' . $phpVersion->toString() . '.');

        return $this;
    }
}
