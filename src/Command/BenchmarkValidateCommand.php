<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkType,
    ComponentConfiguration\ComponentConfiguration
};

class BenchmarkValidateCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('benchmark:validate')
            ->setDescription('Validate configurations and features for a benchmark');
    }

    protected function doExecute(): parent
    {
        $this
            ->runCommand('validate:all')
            ->runCommand('vhost:create');

        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion);
        }

        return $this;
    }

    protected function onError(): parent
    {
        $this->definePhpCliVersion('7.3');

        return parent::onError();
    }

    protected function validateForPhpVersion(string $phpVersion): self
    {
        $url =
            'http://'
            . $this->getHost($phpVersion, false)
            . '/'
            . ComponentConfiguration::getBenchmarkUrl();
        $urlWithPort =
            'http://'
            . $this->getHost($phpVersion)
            . '/'
            . ComponentConfiguration::getBenchmarkUrl();

        $this
            ->title('Validation of ' . $urlWithPort)
            ->definePhpCliVersion($phpVersion)
            ->exec('cd /var/www/phpbenchmarks && ./.phpbenchmarks/initBenchmark.sh')
            ->success('.phpbenchmarks/initBenchmark.sh executed.');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            $this->error('Http code should be 200 but is ' . $httpCode . '.');
        }
        $this->success('Http code is 200.');

        $this->validateBody($body);

        return $this;
    }

    protected function validateBody(string $body): self
    {
        $validated = false;
        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $responseFile = $this->getResponseBodyPath() . '/' . $file;
            if ($body === file_get_contents($responseFile)) {
                $this->success('Body is equal to ' . $responseFile . ' content.');
                $validated = true;
                break;
            }
        }

        if ($validated === false) {
            file_put_contents('/tmp/benchmark.body', $body);
            $this
                ->warning('You canse use "diff /tmp/benchmark.body ' . $responseFile . '" to view differences.')
                ->error('Invalid body, it should be equal to a file in .phpbenchmarks/responseBody.');
        }

        return $this;
    }
}
