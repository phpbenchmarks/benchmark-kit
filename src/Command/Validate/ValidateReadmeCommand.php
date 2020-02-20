<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureReadmeCommand,
    Benchmark\Benchmark,
    Utils\Path
};

final class ValidateReadmeCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:readme';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate README.md');
    }

    protected function onError(): parent
    {
        return $this->outputCallPhpbenchkitWarning(ConfigureReadmeCommand::getDefaultName());
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validation of README.md');

        $readmeFileName = Path::getSourceCodePath() . '/README.md';
        if (is_readable($readmeFileName) === false) {
            throw new \Exception('README.md does not exists or is not readable.');
        }
        $content = file_get_contents($readmeFileName);

        $expectedContent = $this->renderBenchmarkTemplate(
            'README.md',
            [
                'componentName' => Benchmark::getComponentName(),
                'componentSlug' => Benchmark::getComponentSlug(),
                'coreDependencyMajorVersion' => (string) Benchmark::getCoreDependencyMajorVersion(),
                'coreDependencyMinorVersion' => (string) Benchmark::getCoreDependencyMinorVersion()
            ]
        );

        if ($expectedContent !== $content) {
            throw new \Exception('README.md content is not valid.');
        }

        $this->outputSuccess('README.md content is valid.');

        return 0;
    }
}
