<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    SourceCodeUrl\SourceCodeUrl
};
use PhpBenchmarks\BenchmarkConfiguration\Configuration;

final class ConfigurePhpBenchmarksConfigurationClassGetSourceCodeUrlsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:configurationClass:getSourceCodeUrls';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure Configuration::getSourceCodeUrls()');
    }

    protected function doExecute(): AbstractCommand
    {
        return $this
            ->outputTitle('Configuration of Configuration::getSourceCodeUrls()')
            ->defineSourceCodeUrls();
    }

    private function defineSourceCodeUrls(): self
    {
        $reflection = new \ReflectionClass(Configuration::class);
        $method = $reflection->getMethod('getSourceCodeUrls');

        $classCode = file($reflection->getFileName());
        for ($i = $method->getStartLine() + 1; $i < $method->getEndLine(); $i++) {
            unset($classCode[$i]);
        }
        unset($classCode[$reflection->getEndLine() - 1]);

        return $this->filePutContent(
            $reflection->getFileName(),
            implode(null, $classCode) . $this->getUrlsCode() . "\n" . '    }' . "\n" . '}'
        );
    }

    private function getUrlsCode(): string
    {
        $urls = [];
        foreach (BenchmarkType::getSourceCodeUrlIds() as $urlId) {
            $urls[] =
                '            \''
                . $urlId
                . '\' => \''
                . $this->askQuestion(SourceCodeUrl::QUESTIONS[$urlId])
                . '\'';
        }

        return '        return [' . "\n" . implode(",\n", $urls) . "\n" . '        ];';
    }
}
