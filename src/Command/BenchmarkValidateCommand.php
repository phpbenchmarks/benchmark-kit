<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    Benchmark\BenchmarkType,
    ComponentConfiguration\ComponentConfiguration,
    Command\Validate\ValidateComposerLockFilesCommand,
    Component\ComponentType
};

class BenchmarkValidateCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure(): void
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
        $benchmarkUrl = ComponentConfiguration::getBenchmarkUrl();
        $showResultsQueryParameter = ComponentType::getShowResultsQueryParameter(
            ComponentConfiguration::getComponentType()
        );
        if (is_string($showResultsQueryParameter)) {
            $benchmarkUrl .= (strpos($benchmarkUrl, '?') === false) ? '?' : '&';
            $benchmarkUrl .= $showResultsQueryParameter;
        }

        $url =
            'http://'
            . $this->getHost($phpVersion, false)
            . '/'
            . $benchmarkUrl;

        $urlWithPort =
            'http://'
            . $this->getHost($phpVersion)
            . '/'
            . $benchmarkUrl;

        $this
            ->title('Validation of ' . $urlWithPort)
            ->definePhpCliVersion($phpVersion)
            ->exec(
                'cp '
                . $this->getComposerLockFilePath($phpVersion)
                . ' '
                . $this->getInstallationPath()
                . '/composer.lock'
            )
            ->exec(
                'cd '
                . $this->getInstallationPath()
                . ' && ./'
                . $this->getInitBenchmarkFilePath(true)
            )
            ->exec(
                'rm '
                . $this->getInstallationPath()
                . '/composer.lock'
            )
            ->success($this->getInitBenchmarkFilePath(true) . ' executed.');

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
                ->error('Invalid body, it should be equal to a file in ' . $this->getResponseBodyPath(true) . '.');
        }

        return $this;
    }
}
