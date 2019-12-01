<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration,
    Utils\Path
};

final class ConfigureEntryPointCommand extends AbstractCommand
{
    public const STATS_COMMENT = '// require phpbenchmarks stats.php here when needed';

    /** @var string */
    protected static $defaultName = 'configure:entrypoint';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Configure entrypoint');
    }

    protected function doExecute(): parent
    {
        $this->outputTitle('Configure entrypoint');

        $entryPointFileName = Path::getBenchmarkPath() . '/' . ComponentConfiguration::getEntryPointFileName();

        if (is_readable($entryPointFileName) === false) {
            throw new \Exception(
                'Entrypoint ' . Path::rmPrefix($entryPointFileName) . ' is not readable.'
            );
        }

        $content = file_get_contents($entryPointFileName);

        if (strpos($content, static::STATS_COMMENT) === false) {
            $this->filePutContent(
                $entryPointFileName,
                $content . "\n" . static::STATS_COMMENT . "\n"
            );
        }

        return $this;
    }
}
