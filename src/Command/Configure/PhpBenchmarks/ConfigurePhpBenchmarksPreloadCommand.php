<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\CallUrlTrait,
    Command\Benchmark\BenchmarkInitCommand,
    Command\Nginx\Vhost\NginxVhostPreloadGeneratorCreateCommand,
    Command\Nginx\Vhost\NginxVhostPreloadGeneratorDeleteCommand,
    Command\Php\Fpm\PhpFpmRestartCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};
use Symfony\Component\Filesystem\Filesystem;

final class ConfigurePhpBenchmarksPreloadCommand extends AbstractCommand
{
    use CallUrlTrait;

    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:preload';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create preload.php for each compatible PHP version');
    }

    protected function doExecute(): int
    {
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            if ($phpVersion->isPreloadAvailable() === true) {
                $this
                    ->runCommand(
                        PhpFpmRestartCommand::getDefaultName(),
                        ['phpVersion' => $phpVersion->toString()]
                    )
                    ->resetPreloadFile($phpVersion)
                    ->initBenchmark($phpVersion)
                    ->removePreloadFile($phpVersion)
                    ->createPreloadVhost($phpVersion)
                    ->generatePreloadFile($phpVersion)
                    ->deletePreloadVhost();
            }
        }

        $this
            ->outputTitle('Preload files created')
            ->outputSuccess('Preload files for each compatible PHP version has been created.')
            ->outputWarning('Call ' . BenchmarkInitCommand::getDefaultName() . ' to use them.');

        return 0;
    }

    private function resetPreloadFile(PhpVersion $phpVersion): self
    {
        return $this
            ->outputTitle('Reset preload file')
            ->filePutContent(Path::getPreloadPath($phpVersion), '<?php');
    }

    private function removePreloadFile(PhpVersion $phpVersion): self
    {
        (new Filesystem())->remove(Path::getPreloadPath($phpVersion));

        return $this;
    }

    private function initBenchmark(PhpVersion $phpVersion): self
    {
        return $this->runCommand(
            BenchmarkInitCommand::getDefaultName(),
            [
                '--opcache-enabled' => true,
                '--preload-enabled' => true,
                '--no-url-output' => true,
                'phpVersion' => $phpVersion->toString()
            ]
        );
    }

    private function createPreloadVhost(PhpVersion $phpVersion): self
    {
        return $this->runCommand(
            NginxVhostPreloadGeneratorCreateCommand::getDefaultName(),
            [
                'phpVersion' => $phpVersion->toString(),
                '--no-url-output' => true
            ]
        );
    }

    private function deletePreloadVhost(): self
    {
        return $this->runCommand(NginxVhostPreloadGeneratorDeleteCommand::getDefaultName());
    }

    private function generatePreloadFile(PhpVersion $phpVersion): self
    {
        $this->outputTitle('Generating preload file');

        for ($i = 0; $i < 5; $i++) {
            $this->callUrl(BenchmarkUrlService::getUrl(false));
        }

        $this
            ->outputSuccess('Opcache initialized.')
            ->callUrl(BenchmarkUrlService::getPreloadGeneratorUrl());

        if (is_file(Path::getPreloadPath($phpVersion)) === false) {
            throw new \Exception('Preload file ' . Path::rmPrefix(Path::getPreloadPath($phpVersion) . '  not found.'));
        }

        return $this
            ->outputSuccess(Path::rmPrefix(Path::getPreloadPath($phpVersion)) . ' has been created.')
            ->outputWarning('This file has been generated automatically, feel free to update it.');
    }
}
