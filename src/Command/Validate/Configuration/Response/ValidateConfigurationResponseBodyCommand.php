<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration\Response;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Command\Configure\ConfigureResponseBodyCommand,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidateConfigurationResponseBodyCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:response:body';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getResponseBodyPath(new PhpVersion(0, 0))) . ' files');
    }

    protected function doExecute(): int
    {
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $responseBodyPath = Path::getResponseBodyPath($phpVersion);

            $this->outputTitle('Validation of ' . Path::rmPrefix($responseBodyPath) . ' files');
            foreach (BenchmarkType::getResponseBodyFiles(Benchmark::getBenchmarkType()) as $file) {
                $this->validateResponseBodyFile($responseBodyPath . '/' . $file);
            }
        }

        return 0;
    }

    private function validateResponseBodyFile(string $filePath): self
    {
        $this->assertFileExist($filePath, ConfigureResponseBodyCommand::getDefaultName());

        $minSize = BenchmarkType::getResponseBodyFileMinSize(Benchmark::getBenchmarkType());
        $minSizeFormated = number_format($minSize, 0, '.', ',');
        $fileSize = filesize($filePath);
        if ($fileSize < $minSize) {
            throw new \Exception(
                'File '
                . Path::rmPrefix($filePath)
                . ' size must be at least '
                . $minSizeFormated
                . ' bytes but is '
                . number_format($fileSize, 0, '.', ',')
                . '.'
            );
        }

        return $this->outputSuccess(
            'File '
                . Path::rmPrefix($filePath)
                . ' size is >= '
                . $minSizeFormated
                . ' bytes.'
        );
    }
}
