<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Benchmark\Benchmark,
    Benchmark\BenchmarkType,
    Benchmark\BenchmarkUrlService,
    BenchmarkConfiguration\BenchmarkConfiguration,
    Command\Behavior\GetBodyFromUrl,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidateBenchmarkResponseCommand extends AbstractValidateBenchmarkUrlCommand
{
    use GetBodyFromUrl;

    /** @var string */
    protected static $defaultName = 'validate:benchmark:response';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate benchmark response');
    }

    protected function getUrl(): string
    {
        return BenchmarkUrlService::getUrl(true);
    }

    protected function afterHttpCodeValidated(
        PhpVersion $phpVersion,
        BenchmarkConfiguration $benchmarkConfiguration,
        ?string $body
    ): self {
        $validated = false;
        $responseBodyPath = Path::getResponseBodyPath($phpVersion);

        foreach (BenchmarkType::getResponseBodyFiles(Benchmark::getBenchmarkType()) as $file) {
            $responseFile = $responseBodyPath . '/' . $file;
            if ($body === file_get_contents($responseFile)) {
                $this->outputSuccess('Body is equal to ' . Path::rmPrefix($responseFile) . ' content.');
                $validated = true;
                break;
            }
        }

        if ($validated === false) {
            throw new \Exception(
                'Invalid body, it should be equal to a file in ' . Path::rmPrefix($responseBodyPath) . '.'
            );
        }

        return $this;
    }
}
