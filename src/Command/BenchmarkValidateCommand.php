<?php

declare(strict_types=1);

namespace App\Command;

use App\ComponentConfiguration\ComponentConfiguration;

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
        $this->runCommand('validate:all');

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
            'http://php'
            . str_replace('.', null, $phpVersion)
            . '.benchmark.loc/'
            . ComponentConfiguration::getBenchmarkUrl();

        $this
            ->title('Validation of ' . $url)
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
        $responseFile = $this->getResponseBodyPath() . '/responseBody.txt';
        if ($body !== file_get_contents($responseFile)) {
            file_put_contents('/tmp/benchmark.body', $body);
            $this
                ->warning('You canse use "diff /tmp/benchmark.body ' . $responseFile . '" to view differences.')
                ->error('Invalid body, it should be equal to .phpbenchmarks/responseBody/responseBody.txt content.');
        }
        $this->success('Body is equal to ' . $responseFile . ' content.');

        return $this;
    }
}
