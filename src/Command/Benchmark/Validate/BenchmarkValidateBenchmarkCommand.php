<?php

declare(strict_types=1);

namespace App\Command\Benchmark\Validate;

use App\{
    Benchmark\BenchmarkType,
    Benchmark\BenchmarkUrlService,
    Command\AbstractCommand,
    Command\Benchmark\BenchmarkInitCommand,
    Command\PrepareBenchmarkCurlTrait,
    Command\Validate\ValidateAllCommand,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class BenchmarkValidateBenchmarkCommand extends AbstractCommand
{
    use PrepareBenchmarkCurlTrait;

    /** @var string */
    protected static $defaultName = 'benchmark:validate:benchmark';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate configurations and features for the benchmark')
            ->addOption('no-validate')
            ->addOption('no-url-output');
    }

    protected function doExecute(): parent
    {
        if ($this->getInput()->getOption('no-validate') === false) {
            $this->runCommand(ValidateAllCommand::getDefaultName());
        }

        foreach (ComponentConfiguration::getCompatiblesPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion);
        }

        return $this;
    }

    private function validateForPhpVersion(PhpVersion $phpVersion): self
    {
        $this
            ->runCommand(
                BenchmarkInitCommand::getDefaultName(),
                [
                    'phpVersion' => $phpVersion->toString(),
                    '--no-url-output' => $this->getInput()->getOption('no-url-output')
                ]
            )
            ->outputTitle('Validation of ' . BenchmarkUrlService::getUrlWithPort(true));

        $curl = $this->prepareBenchmarkCurl(true);
        $body = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new \Exception('Http code should be 200 but is ' . $httpCode . '.');
        }

        return $this
            ->outputSuccess('Http code is 200.')
            ->validateBody($body, $phpVersion);
    }

    private function validateBody(string $body, PhpVersion $phpVersion): self
    {
        $validated = false;
        $responseBodyPath = Path::getResponseBodyPath($phpVersion);

        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
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
