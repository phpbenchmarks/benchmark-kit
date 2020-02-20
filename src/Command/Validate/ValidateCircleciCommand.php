<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureCircleCiCommand,
    Utils\Path
};

final class ValidateCircleciCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:circleci';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getCircleCiPath()));
    }

    protected function onError(): parent
    {
        return $this->outputCallPhpbenchkitWarning(ConfigureCircleCiCommand::getDefaultName());
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Validation of ' . Path::rmPrefix(Path::getCircleCiPath()));

        $configFilePath = Path::getCircleCiPath() . '/config.yml';
        $relativeConfigFilePath = Path::rmPrefix($configFilePath);
        if (is_readable($configFilePath) === false) {
            throw new \Exception("$relativeConfigFilePath does not exists or is not readable.");
        }
        $content = file_get_contents($configFilePath);

        $expectedContent = $this->renderBenchmarkTemplate($relativeConfigFilePath);

        if ($expectedContent !== $content) {
            throw new \Exception("$relativeConfigFilePath content is not valid.");
        }

        $this->outputSuccess("$relativeConfigFilePath content is valid.");

        return 0;
    }
}
