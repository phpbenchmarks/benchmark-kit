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
    Utils\Path
};

final class NginxVhostBenchmarkKitCreateCommand extends AbstractCommand
{
    use DefineVhostVariablesTrait;
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    /** @var string */
    protected static $defaultName = 'nginx:vhost:benchmark-kit:create';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . BenchmarkUrlService::HOST)
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Create ' . BenchmarkUrlService::HOST . ' virtual host')
            ->assertPhpVersionArgument($this->getInput())
            ->createVhostFile()
            ->defineVhostVariables(
                $this->getContainerVhostFilePath(),
                $this->getPhpVersionFromArgument($this->getInput()),
                BenchmarkUrlService::HOST,
                Benchmark::getSourceCodeEntryPoint(),
                [$this, 'filePutContent'],
                [$this, 'outputSuccess']
            );

        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        if ($this->getInput()->getOption('no-url-output') === false) {
            $this->outputUrl();
        }

        return 0;
    }

    private function getContainerVhostFilePath(): string
    {
        return Path::getNginxVhostPath() . '/benchmark-kit.loc.conf';
    }

    private function createVhostFile(): self
    {
        $destination = $this->getContainerVhostFilePath();

        return $this
            ->runProcess(['cp', Path::getVhostPath(), $destination])
            ->outputSuccess('Create ' . $destination . '.');
    }

    private function outputUrl(): self
    {
        $this->getOutput()->writeln('');

        return $this->outputBlock(
            [
                '',
                'You can test your code at this url: ' . BenchmarkUrlService::getUrl(false),
                ''
            ],
            'green',
            $this->getOutput()
        );
    }
}
