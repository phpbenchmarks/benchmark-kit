<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};

class ValidateConfigurationResponseBodyCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('validate:configuration:responseBody')
            ->setDescription('Validate ' . $this->getResponseBodyPath(true) . ' files');
    }

    protected function doExecute(): parent
    {
        $this->title('Validation of ' . $this->getResponseBodyPath(true) . ' files');

        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $filePath = $this->getResponseBodyPath() . '/' . $file;
            $fileRelativePath = $this->getResponseBodyPath(true) . '/' . $file;
            $this->assertFileExist($filePath, $fileRelativePath);

            $minSize = BenchmarkType::getResponseBodyFileMinSize(ComponentConfiguration::getBenchmarkType());
            $minSizeFormated = number_format($minSize, 0, '.', ',');
            $fileSize = filesize($filePath);
            ($fileSize < $minSize)
                ?
                    $this->error(
                        'File '
                        . $fileRelativePath
                        . ' size must be at least '
                        . $minSizeFormated
                        . ' bytes but is '
                        . number_format($fileSize, 0, '.', ',')
                        . '.'
                    )
                : $this->success('File ' . $fileRelativePath . ' size is >= ' . $minSizeFormated . ' bytes.');
        }

        return $this;
    }
}
