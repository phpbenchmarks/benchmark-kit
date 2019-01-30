<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\Command\AbstractCommand;

class ValidateConfigurationResponseBodyCommand extends AbstractCommand
{
    /** @var ?string */
    protected $vhostContent;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:configuration:responseBody')
            ->setDescription('Validate .phpbenchmarks/responseBody files');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Validation of .phpbenchmarks/responseBody')
            ->assertFileExist(
                $this->getResponseBodyPath() . '/responseBody.txt',
                '.phpbenchmarks/responseBody/responseBody.txt'
            );

        return $this;
    }
}
