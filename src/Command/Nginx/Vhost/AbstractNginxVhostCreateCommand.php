<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\OutputBlockTrait,
    Command\Behavior\PhpVersionArgumentTrait,
    Command\Behavior\ReloadNginxTrait,
    PhpVersion\PhpVersion,
    Utils\Path
};

abstract class AbstractNginxVhostCreateCommand extends AbstractCommand
{
    use OutputBlockTrait;
    use PhpVersionArgumentTrait;
    use ReloadNginxTrait;

    abstract protected function getHost(): string;

    abstract protected function getContainerVhostFileName(): string;

    abstract protected function getOutputUrlMessage(): string;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create nginx vhost ' . $this->getHost())
            ->addPhpVersionArgument($this)
            ->addOption('no-url-output')
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Create ' . $this->getHost() . ' virtual host')
            ->assertPhpVersionArgument($this->getInput())
            ->createVhostFile()
            ->defineVhostVariables(
                Path::getNginxVhostPath() . '/' . $this->getContainerVhostFileName(),
                $this->getPhpVersionFromArgument($this->getInput()),
                $this->getHost(),
                $this->getEntryPointPath(),
                $this->getInstallationPath(),
                [$this, 'filePutContent'],
                [$this, 'outputSuccess']
            )
            ->onVhostCreated();

        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        if ($this->getInput()->getOption('no-url-output') === false) {
            $this->outputUrl();
        }

        return 0;
    }

    protected function getEntryPointPath(): string
    {
        return Benchmark::getSourceCodeEntryPoint();
    }

    protected function getVhostTemplatePath(): string
    {
        return Path::getVhostPath();
    }

    protected function getInstallationPath(): string
    {
        return Path::getSourceCodePath();
    }

    protected function onVhostCreated(): self
    {
        return $this;
    }

    protected function createVhostFile(): self
    {
        $destination = Path::getNginxVhostPath() . '/' . $this->getContainerVhostFileName();

        return $this
            ->runProcess(['cp', $this->getVhostTemplatePath(), $destination])
            ->outputSuccess("Create $destination.");
    }

    protected function outputUrl(): self
    {
        $this->getOutput()->writeln('');

        return $this->outputBlock(
            ['', $this->getOutputUrlMessage(), ''],
            'green',
            $this->getOutput()
        );
    }

    protected function defineVhostVariables(
        string $vhostFilePath,
        PhpVersion $phpVersion,
        string $host,
        string $entryPointRelativePath,
        string $sourceCodePath,
        callable $filePutContent,
        callable $outputSuccess
    ): self {
        $content = file_get_contents($vhostFilePath);
        if ($content === false) {
            throw new \Exception('Error while reading ' . $vhostFilePath . '.');
        }

        $content = str_replace('____PORT____', (string) BenchmarkUrlService::getNginxPort(), $content);
        $content = str_replace('____HOST____', $host, $content);

        $sourceCodePath = realpath($sourceCodePath);
        if ($sourceCodePath === false) {
            throw new \Exception('Source code path "' . Path::getSourceCodePath() . '" not found.');
        }
        $content = str_replace('____INSTALLATION_PATH____', $sourceCodePath, $content);

        $phpFpm = 'php' . $phpVersion->toString() . '-fpm.sock';
        $content = str_replace('____PHP_FPM_SOCK____', $phpFpm, $content);

        $entryPointFilePath = dirname($entryPointRelativePath);
        if ($entryPointFilePath === '.') {
            $entryPointFilePath = null;
        }
        $entryPointFileName = basename($entryPointRelativePath);
        $content = str_replace('____ENTRY_POINT_FILE_PATH____', $entryPointFilePath, $content);
        $content = str_replace('____ENTRY_POINT_FILE_NAME____', $entryPointFileName, $content);

        call_user_func_array($filePutContent, [$vhostFilePath, $content]);

        call_user_func($outputSuccess, '____PORT____ replaced by ' . BenchmarkUrlService::getNginxPort() . '.');
        call_user_func($outputSuccess, "____HOST____ replaced by $host.");
        call_user_func($outputSuccess, "____INSTALLATION_PATH____ replaced by $sourceCodePath.");
        call_user_func($outputSuccess, "____PHP_FPM_SOCK____ replaced by $phpFpm.");
        call_user_func($outputSuccess, "____ENTRY_POINT_FILE_PATH____ replaced by $entryPointFilePath.");
        call_user_func($outputSuccess, "____ENTRY_POINT_FILE_NAME____ replaced by $entryPointFileName.");

        return $this;
    }
}
