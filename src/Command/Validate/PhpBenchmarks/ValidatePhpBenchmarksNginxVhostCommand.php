<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksNginxVhostCommand,
    Utils\Path
};
use steevanb\PhpTypedArray\ScalarArray\StringArray;

final class ValidatePhpBenchmarksNginxVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:nginx:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::getVhostPath());
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle('Validation of ' . Path::rmPrefix(Path::getVhostPath()))
            ->assertFileExist(Path::getVhostPath(), ConfigurePhpBenchmarksNginxVhostCommand::getDefaultName())
            ->assertContainsVariables(
                new StringArray(
                    ['____HOST____', '____INSTALLATION_PATH____', '____PHP_FPM_SOCK____']
                )
            );
    }

    private function assertContainsVariables(StringArray $variables): self
    {
        $vhostContent = file_get_contents(Path::getVhostPath());

        foreach ($variables as $variable) {
            if (strpos($vhostContent, $variable) === false) {
                throw new \Exception('File should contains ' . $variable . ' variable.');
            }

            $this->outputSuccess('File contains ' . $variable . ' variable.');
        }

        return $this;
    }
}
