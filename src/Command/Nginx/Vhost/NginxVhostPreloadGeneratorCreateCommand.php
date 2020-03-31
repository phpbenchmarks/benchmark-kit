<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\DefineVhostVariablesTrait,
    Command\Behavior\OutputBlockTrait,
    Command\Behavior\PhpVersionArgumentTrait,
    Command\Behavior\ReloadNginxTrait,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class NginxVhostPreloadGeneratorCreateCommand extends AbstractCommand
{
    use DefineVhostVariablesTrait;
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    /** @var string */
    protected static $defaultName = 'nginx:vhost:preloadGenerator:create';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . BenchmarkUrlService::PRELOAD_GENERATOR_HOST)
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Create ' . BenchmarkUrlService::PRELOAD_GENERATOR_HOST . ' virtual host')
            ->assertPhpVersionArgument($this->getInput());

        $phpVersion = $this->getPhpVersionFromArgument($this->getInput());
        if ($phpVersion->isPreloadAvailable() === false) {
            throw new \Exception('Preload is not available for PHP ' . $phpVersion->toString() . '.');
        }

        $this
            ->createVhostFile()
            ->createPublicFile($phpVersion)
            ->defineVhostVariables(
                $this->getContainerVhostFilePath(),
                $this->getPhpVersionFromArgument($this->getInput()),
                BenchmarkUrlService::PRELOAD_GENERATOR_HOST,
                Benchmark::getPublicPath() . '/' . Path::getPreloadEntryPointName(),
                [$this, 'filePutContent'],
                [$this, 'outputSuccess']
            );

        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this, true);
        }

        if ($this->getInput()->getOption('no-url-output') === false) {
            $this->outputUrl();
        }

        return 0;
    }

    private function createVhostFile(): self
    {
        $destination = $this->getContainerVhostFilePath();

        return $this
            ->runProcess(['cp', Path::getVhostPath(), $destination])
            ->outputSuccess('Create ' . $destination . '.');
    }

    private function getContainerVhostFilePath(): string
    {
        return Path::getNginxVhostPath() . '/preload-generator.benchmark-kit.loc.conf';
    }

    private function createPublicFile(PhpVersion $phpVersion): self
    {
        $preloaderRelativePath = dirname(Benchmark::getSourceCodeEntryPoint()) . '/' . Path::getPreloadEntryPointName();

        return $this
            ->filePutContent(
                Path::getSourceCodePath() . "/$preloaderRelativePath",
                $this->renderBenchmarkTemplate(
                    Path::getPreloadEntryPointName(),
                    [
                        'sourceCodePath' => Path::getSourceCodePath(),
                        'sourceCodeRelativePath' => '/../../../',
                        'entryPointPath' =>
                            realpath(Path::getSourceCodePath()) . '/' . Benchmark::getSourceCodeEntryPoint(),
                        'preloadPath' => Path::getPreloadPath($phpVersion)
                    ]
                ),
                true
            );
    }

    private function outputUrl(): self
    {
        $this->getOutput()->writeln('');

        return $this->outputBlock(
            [
                '',
                'You can test your code at this url: ' . BenchmarkUrlService::getPreloadGeneratorUrl(),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
