<?php

declare(strict_types=1);

namespace App\Command\Benchmark;

use App\{Command\AbstractCommand,
    Command\PhpVersion\PhpVersionCliDefineCommand,
    Command\PhpVersionArgumentTrait,
    Command\Nginx\NginxVhostCreateCommand,
    Composer\ComposerService,
    Utils\Directory};
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
        $composerLockFilePath = Directory::getComposerLockFilePath($phpVersion);

        return $this
            ->assertPhpVersionArgument($this)
            ->runCommand(NginxVhostCreateCommand::getDefaultName(), ['phpVersion' => $phpVersion->toString()])
            ->runCommand(PhpVersionCliDefineCommand::getDefaultName(), ['phpVersion' => $phpVersion->toString()])
            ->outputTitle('Prepare composer.lock')
            ->runProcess(['cp', $composerLockFilePath, 'composer.lock'])
            ->outputSuccess($composerLockFilePath . ' copied to composer.lock.')
            ->outputTitle('Call ' . $initBenchmarkShortPath)
            ->runProcess([$this->getInitBenchmarkFilePath()], OutputInterface::VERBOSITY_VERBOSE)
            ->outputSuccess($initBenchmarkShortPath . ' called.')
            ->removeFile($this->getInstallationPath() . '/composer.lock');
    }
}
