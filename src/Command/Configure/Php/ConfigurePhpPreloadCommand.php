<?php

declare(strict_types=1);

namespace App\Command\Configure\Php;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Behavior\GetBodyFromUrl,
    Command\Benchmark\BenchmarkInitCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};
use Symfony\Component\Filesystem\Filesystem;

final class ConfigurePhpPreloadCommand extends AbstractCommand
{
    use GetBodyFromUrl;

    /** @var string */
    protected static $defaultName = 'configure:php:preload';

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
                    ->resetPreloadFile($phpVersion)
                    ->initBenchmark($phpVersion)
                    ->generatePreloadFile($phpVersion);
            }
        }

        return 0;
    }

    private function resetPreloadFile(PhpVersion $phpVersion): self
    {
        (new Filesystem())->dumpFile(Path::getPreloadPath($phpVersion), '<?php');

        return $this;
    }

    private function initBenchmark(PhpVersion $phpVersion): self
    {
        return $this->runCommand(
            BenchmarkInitCommand::getDefaultName(),
            [
                '--opcache-enabled' => true,
                '--preload-enabled' => false,
                '--no-url-output' => true,
                'phpVersion' => $phpVersion->toString()
            ]
        );
    }

    private function generatePreloadFile(PhpVersion $phpVersion): self
    {
        $this->outputTitle('Generating preload file for PHP ' . $phpVersion->toString());

        for ($i = 0; $i < 5; $i++) {
            $this->getBodyFromUrl(BenchmarkUrlService::getUrl(false));
        }

        $this->outputSuccess('Opcache initialized.');
        try {
            $this->getBodyFromUrl(BenchmarkUrlService::getPreloadGeneratorUrl());
        } catch (\Throwable $exception) {
            $this->outputWarning('Preload has not been generated: ' . $exception->getMessage());

            return $this;
        }

        if (is_file(Path::getPreloadPath($phpVersion)) === false) {
            $this->outputWarning(
                'Preload has not been generated: Preload file '
                    . Path::rmPrefix(Path::getPreloadPath($phpVersion))
                    . '  not found.'
            );

            return $this;
        }

        return $this
            ->outputSuccess(Path::rmPrefix(Path::getPreloadPath($phpVersion)) . ' has been created.')
            ->outputWarning('This file has been generated automatically, feel free to update it.');
    }
}
