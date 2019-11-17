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

final class BenchmarkInitBenchmarkCommand extends AbstractCommand
{
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'benchmark:init-benchmark';

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

        $this
            ->assertPhpVersionArgument($this)
            ->runCommand(VhostCreateCommand::getDefaultName(), ['phpVersion' => $phpVersion])
            ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion])
            ->outputTitle('Init benchmark for PHP ' . $phpVersion)
            ->runProcess(['cp', $this->getComposerLockFilePath($phpVersion), 'composer.lock'])
            ->outputSuccess($this->getComposerLockFilePath($phpVersion, true) . ' copied to composer.lock.')
            ->runProcess([$this->getInitBenchmarkFilePath()], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess($this->getInitBenchmarkFilePath() . ' called.');

        return $this;
    }
}
