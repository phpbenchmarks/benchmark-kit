<?php

declare(strict_types=1);

namespace App\Command\Validate\Benchmark;

use App\{
    Benchmark\BenchmarkUrlService,
    BenchmarkConfiguration\BenchmarkConfiguration,
    Command\Behavior\GetBodyFromUrl,
    PhpVersion\PhpVersion
};

final class ValidateBenchmarkPhpInfoCommand extends AbstractValidateBenchmarkUrlCommand
{
    use GetBodyFromUrl;

    /** @var string */
    protected static $defaultName = 'validate:benchmark:phpinfo';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate benchmark response');
    }

    protected function getUrl(): string
    {
        return BenchmarkUrlService::getPhpinfoUrl();
    }

    protected function afterHttpCodeValidated(
        PhpVersion $phpVersion,
        BenchmarkConfiguration $benchmarkConfiguration,
        ?string $body
    ): self {
        if (is_string($body) === false || strlen($body) === 0) {
            throw new \Exception('phpinfo() should not output an empty string.');
        }

        return $this;
    }
}
