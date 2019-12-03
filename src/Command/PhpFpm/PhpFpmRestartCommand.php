<?php

declare(strict_types=1);

namespace App\Command\PhpFpm;

use App\{
    Command\AbstractCommand,
    Command\PhpVersionArgumentTrait
};
use Symfony\Component\Console\Output\OutputInterface;

final class PhpFpmRestartCommand extends AbstractCommand
{
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'phpFpm:restart';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Restart PHP-FPM')
            ->addPhpVersionArgument($this);
    }

    protected function doExecute(): parent
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);

        return $this
            ->outputTitle('Restart PHP-FPM ' . $phpVersion->toString())
            ->assertPhpVersionArgument($this)
            ->runProcess(
                ['sudo', '/usr/sbin/service', 'php' . $phpVersion->toString() . '-fpm', 'restart'],
                OutputInterface::VERBOSITY_VERBOSE
            )
            ->outputSuccess('PHP-FPM ' . $phpVersion->toString() . ' restarted.');
    }
}
