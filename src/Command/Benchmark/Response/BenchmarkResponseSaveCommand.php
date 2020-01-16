<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Response;

use App\{
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\CallBenchmarkUrlTrait,
    Utils\Path
};
use Symfony\Component\Console\Input\InputArgument;

final class BenchmarkResponseSaveCommand extends AbstractCommand
{
    use CallBenchmarkUrlTrait;

    /** @var string */
    protected static $defaultName = 'benchmark:response:save';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Save benchmark response')
            ->addArgument('file', InputArgument::OPTIONAL, 'Benchmark response file name', 'benchmarkResponse.txt');
    }

    protected function doExecute(): parent
    {
        $fileName = $this->getBenchmarkFileName();
        $this->outputTitle("Save benchmark response to $fileName");

        return $this->filePutContent($fileName, $this->getBenchmarkResponse());
    }

    private function getBenchmarkFileName(): string
    {
        $fileName = $this->getInput()->getArgument('file');
        if (substr($fileName, 0, 1) !== '/') {
            $fileName = Path::getBenchmarkPath() . '/' . $fileName;
        }

        return $fileName;
    }

    private function getBenchmarkResponse(): string
    {
        $benchmarkUrl = BenchmarkUrlService::getUrl(false);
        $response = $this->callBenchmarkUrl($benchmarkUrl, false);
        $this->outputSuccess("$benchmarkUrl called.");

        return $response;
    }
}
