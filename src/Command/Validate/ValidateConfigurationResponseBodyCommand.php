<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

final class ValidateConfigurationResponseBodyCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:response-body';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . $this->getResponseBodyPath(true) . ' files');
    }

    protected function doExecute(): parent
    {
        $this->outputTitle('Validation of ' . $this->getResponseBodyPath(true) . ' files');

        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $filePath = $this->getResponseBodyPath() . '/' . $file;
            $fileRelativePath = $this->getResponseBodyPath(true) . '/' . $file;
            $this->assertFileExist($filePath, $fileRelativePath);

            $minSize = BenchmarkType::getResponseBodyFileMinSize(ComponentConfiguration::getBenchmarkType());
            $minSizeFormated = number_format($minSize, 0, '.', ',');
            $fileSize = filesize($filePath);
            ($fileSize < $minSize)
                ?
                    $this->throwError(
                        'File '
                        . $fileRelativePath
                        . ' size must be at least '
                        . $minSizeFormated
                        . ' bytes but is '
                        . number_format($fileSize, 0, '.', ',')
                        . '.'
                    )
                : $this->outputSuccess('File ' . $fileRelativePath . ' size is >= ' . $minSizeFormated . ' bytes.');
        }

        return $this;
    }
}
