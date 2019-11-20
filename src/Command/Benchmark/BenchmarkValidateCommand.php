<?php

declare(strict_types=1);

namespace App\Command\Benchmark;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Validate\ValidateAllCommand,
    Command\Vhost\VhostCreateCommand,
    ComponentConfiguration\ComponentConfiguration,
    Component\ComponentType
};

final class BenchmarkValidateCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'benchmark:validate';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate configurations and features for the benchmark');
    }

    protected function doExecute(): parent
    {
        $this->runCommand(ValidateAllCommand::getDefaultName());

        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $this->validateForPhpVersion($phpVersion);
        }

        return $this;
    }

    private function validateForPhpVersion(string $phpVersion): self
    {
        $benchmarkUrl = ComponentConfiguration::getBenchmarkUrl();
        $showResultsQueryParameter = ComponentType::getShowResultsQueryParameter(
            ComponentConfiguration::getComponentType()
        );
        if (is_string($showResultsQueryParameter)) {
            $benchmarkUrl .= (strpos($benchmarkUrl, '?') === false) ? '?' : '&';
            $benchmarkUrl .= $showResultsQueryParameter;
        }

        $url = 'http://' . VhostCreateCommand::HOST . $benchmarkUrl;
        $urlWithPort = 'http://' . VhostCreateCommand::HOST . ':' . getenv('NGINX_PORT') . $benchmarkUrl;

        $this
            ->runCommand(BenchmarkInitCommand::getDefaultName(), ['phpVersion' => $phpVersion])
            ->outputTitle('Validation of ' . $urlWithPort);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            $this->throwError('Http code should be 200 but is ' . $httpCode . '.');
        }

        $this
            ->outputSuccess('Http code is 200.')
            ->validateBody($body);

        return $this;
    }

    private function validateBody(string $body): self
    {
        $validated = false;
        foreach (BenchmarkType::getResponseBodyFiles(ComponentConfiguration::getBenchmarkType()) as $file) {
            $responseFile = $this->getResponseBodyPath() . '/' . $file;
            if ($body === file_get_contents($responseFile)) {
                $this->outputSuccess('Body is equal to ' . $responseFile . ' content.');
                $validated = true;
                break;
            }
        }

        if ($validated === false) {
            $this->throwError(
                'Invalid body, it should be equal to a file in ' . $this->getResponseBodyPath(true) . '.'
            );
        }

        return $this;
    }
}
