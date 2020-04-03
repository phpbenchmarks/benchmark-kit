<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration\Nginx;

use App\{
    Command\AbstractCommand,
    Command\Configure\ConfigureNginxVhostCommand,
    Utils\Path
};
use steevanb\PhpTypedArray\ScalarArray\StringArray;

final class ValidateConfigurationNginxVhostCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:nginx:vhost';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getVhostPath()));
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle('Validation of ' . Path::rmPrefix(Path::getVhostPath()))
            ->assertFileExist(Path::getVhostPath(), ConfigureNginxVhostCommand::getDefaultName())
            ->assertContainsVariables(
                new StringArray(
                    [
                        '____PORT____',
                        '____HOST____',
                        '____INSTALLATION_PATH____',
                        '____PHP_FPM_SOCK____'
                    ]
                )
            );

        return 0;
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
