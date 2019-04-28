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
            ->setDescription('Validate .phpbenchmarks/responseBody files');
    }

    protected function doExecute(): parent
    {
        $this->title('Validation of .phpbenchmarks/responseBody files');

        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $filePath = $this->getResponseBodyPath() . '/' . $file;
            $fileRelativePath = '.phpbenchmarks/responseBody/' . $file;
            $this->assertFileExist($filePath, $fileRelativePath);
            // 7,621 is the smallest size possible for the final json, without any space or new line
            $fileSize = filesize($filePath);
            ($fileSize < 7621)
                ? $this->error('File ' . $fileRelativePath . ' size must be at least 7,621 bytes.')
                : $this->success('File ' . $fileRelativePath . ' size is >= 7,621 bytes.');
        }

        return $this;
    }
}
