<?php

declare(strict_types=1);

namespace App\Command\Benchmark;

use App\{
    Command\AbstractCommand,
    Command\PhpVersion\PhpVersionCliDefineCommand,
    Command\PhpVersionArgumentTrait,
    Command\Vhost\VhostCreateCommand
};
use Symfony\Component\Console\Output\OutputInterface;

final class BenchmarkInitCommand extends AbstractCommand
{
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'benchmark:init';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Define PHP version and call ' . $this->getInitBenchmarkFilePath(true))
            ->addPhpVersionArgument($this);
    }

    protected function doExecute(): parent
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);
        $initBenchmarkShortPath = $this->removeInstallationPathPrefix($this->getInitBenchmarkFilePath());

        return $this
            ->assertPhpVersionArgument($this)
            ->runCommand(VhostCreateCommand::getDefaultName(), ['phpVersion' => $phpVersion])
            ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion])
            ->outputTitle('Prepare composer.lock')
            ->runProcess(['cp', $this->getComposerLockFilePath($phpVersion), 'composer.lock'])
            ->outputSuccess($this->getComposerLockFilePath($phpVersion, true) . ' copied to composer.lock.')
            ->outputTitle('Call ' . $initBenchmarkShortPath)
            ->runProcess([$this->getInitBenchmarkFilePath()], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess($initBenchmarkShortPath . ' called.');
    }
}
