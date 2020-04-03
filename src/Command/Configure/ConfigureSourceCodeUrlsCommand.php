<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Validate\Configuration\ValidateConfigurationSourceCodeUrlsCommand,
    SourceCodeUrl\SourceCodeUrl,
    Utils\Path
};
use steevanb\PhpTypedArray\ScalarArray\StringArray;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;

final class ConfigureSourceCodeUrlsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:source-code-urls';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure sourceCode.urls in ' . Path::rmPrefix(Path::getConfigFilePath()));
        foreach (SourceCodeUrl::QUESTIONS as $id => $question) {
            $this->addOption("url-$id", null, InputOption::VALUE_REQUIRED, $question);
        }
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Configuration of ' . Path::rmPrefix(Path::getConfigFilePath()) . ' sourceCode.urls')
            ->defineSourceCodeUrls();

        return 0;
    }

    private function defineSourceCodeUrls(): self
    {
        try {
            $currentConfig = Yaml::parseFile(Path::getConfigFilePath());
        } catch (\Throwable $exception) {
            $currentConfig = [];
        }

        return $this->filePutContent(
            Path::getConfigFilePath(),
            Yaml::dump(
                array_merge(
                    $currentConfig,
                    [
                        'sourceCode' => [
                            'entryPoint' => $currentConfig['sourceCode']['entryPoint'] ?? null,
                            'urls' => $this->getUrls()->toArray()
                        ]
                    ]
                ),
                100
            )
        );
    }

    private function getUrls(): StringArray
    {
        $urls = new StringArray();
        foreach (BenchmarkType::getSourceCodeUrlIds() as $urlId) {
            $url = $this->getInput()->getOption("url-$urlId");
            $errorsCount = 0;
            while (ValidateConfigurationSourceCodeUrlsCommand::validateUrl($url)->count() > 0) {
                $this->outputError('Invalid url.');
                if ($errorsCount >= 5) {
                    throw new \Exception('Invalid url.');
                }
                $errorsCount++;
                $url = $this->askQuestion(SourceCodeUrl::QUESTIONS[$urlId]);
            }

            $urls[$urlId] = $url;
        }

        return $urls;
    }
}
