<?php

declare(strict_types=1);

namespace App\Command\Nginx\Vhost;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\DefineVhostVariablesTrait,
    Command\Behavior\OutputBlockTrait,
    Command\Behavior\ReloadNginxTrait,
    Utils\Path
};

final class NginxVhostPreloadGeneratorDeleteCommand extends AbstractCommand
{
    use DefineVhostVariablesTrait;
    use OutputBlockTrait;
    use ReloadNginxTrait;

    /** @var string */
    protected static $defaultName = 'nginx:vhost:preload-generator:delete';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Delete nginx vhost ' . BenchmarkUrlService::PRELOAD_GENERATOR_HOST)
            ->addOption('no-nginx-reload');
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Delete ' . BenchmarkUrlService::HOST . ' virtual host');

        $this
            ->removeFile(
                Path::getSourceCodePath() . '/' . Benchmark::getPublicPath() . '/' . Path::getPreloadEntryPointName()
            )
            ->removeFile(Path::getNginxVhostPath() . '/preload-generator.benchmark-kit.loc.conf');

        if ($this->getInput()->getOption('no-nginx-reload') === false) {
            $this->reloadNginx($this);
        }

        return 0;
    }
}
