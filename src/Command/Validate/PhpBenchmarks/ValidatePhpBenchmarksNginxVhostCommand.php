<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksNginxVhostCommand,
    Utils\Path
};

final class ValidatePhpBenchmarksNginxVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:nginx:vhost';

    private ?string $vhostContent;

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::getVhostFilePath());
    }

    protected function doExecute(): parent
    {
        $vhostFilePath = Path::getBenchmarkConfigurationPath() . '/' . Path::getVhostFilePath();

        return $this
            ->outputTitle('Validation of ' . $this->removeInstallationPathPrefix($vhostFilePath))
            ->assertFileExist($vhostFilePath, ConfigurePhpBenchmarksNginxVhostCommand::getDefaultName())
            ->assertContainsVariable('____HOST____')
            ->assertContainsVariable('____INSTALLATION_PATH____')
            ->assertContainsVariable('____PHP_FPM_SOCK____');
    }

    private function assertContainsVariable(string $name): self
    {
        if ($this->vhostContent === null) {
            $this->vhostContent = file_get_contents(
                Path::getBenchmarkConfigurationPath() . '/' . Path::getVhostFilePath()
            );
        }

        if (strpos($this->vhostContent, $name) === false) {
            $this->throwError('File should contains ' . $name . ' variable.');
        }
        $this->outputSuccess('File contains ' . $name . ' variable.');

        return $this;
    }
}
