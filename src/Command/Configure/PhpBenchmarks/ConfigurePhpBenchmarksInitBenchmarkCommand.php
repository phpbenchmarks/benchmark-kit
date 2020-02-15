<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    PhpVersion\PhpVersion,
    Utils\Path
};
use Symfony\Component\Console\Input\InputOption;

final class ConfigurePhpBenchmarksInitBenchmarkCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:initBenchmark';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create initBenchmark.sh')
            ->addOption('template', null, InputOption::VALUE_REQUIRED, 'Template file for initBenchmark.sh');
    }

    protected function doExecute(): AbstractCommand
    {
        $template = $this->getInput()->getOption('template');
        /** @var PhpVersion $phpVersion */
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $initBenchmarkPath = Path::getInitBenchmarkPath($phpVersion);
            $initBenchmarkRelativePath = Path::rmPrefix($initBenchmarkPath);

            $this->outputTitle('Creation of ' . $initBenchmarkRelativePath);

            if (is_string($template)) {
                $this->writeFileFromTemplate($template, [], $initBenchmarkRelativePath);
            } else {
                $this->writeFileFromBenchmarkTemplate($initBenchmarkRelativePath);
            }

            $this
                ->runProcess(['chmod', '+x', $initBenchmarkPath])
                ->outputSuccess('Make ' . $initBenchmarkRelativePath . ' executable.');

            if ($template === null) {
                $this->outputWarning(
                    "$initBenchmarkRelativePath (called to initialize your benchmark) has been created."
                        . ' Feel free to edit it.'
                );
            }
        }

        return $this;
    }
}
