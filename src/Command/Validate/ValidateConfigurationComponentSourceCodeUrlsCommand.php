<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\ConfigureConfigurationClassSourceCodeUrlsCommand,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Validator\{
    Constraints\NotBlank,
    Constraints\Type,
    Constraints\Url,
    ConstraintViolationListInterface,
    Validation
};

final class ValidateConfigurationComponentSourceCodeUrlsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:configuration-class:source-code-urls';

    public static function validateSourCodeUrl($url): ConstraintViolationListInterface
    {
        return Validation::createValidator()->validate(
            $url,
            [
                new NotBlank(),
                new Type(['type' => 'string']),
                new Url()
            ]
        );
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . $this->getConfigurationFilePath(true) . '::getSourceCodeUrls()');
    }

    protected function doExecute(): parent
    {
        $this
            ->outputTitle('Validation of ' . $this->getConfigurationFilePath(true) . '::getSourceCodeUrls()')
            ->assertCodeSourceUrls();

        return $this;
    }

    protected function onError(): parent
    {
        $this
            ->outputWarning(
                'You can call "phpbenchkit '
                    . ConfigureConfigurationClassSourceCodeUrlsCommand::getDefaultName()
                    . '" to configure it.'
            )
            ->outputWarning('You can add --skip-source-code-urls parameter to skip this validation.');

        return $this;
    }

    private function assertCodeSourceUrls(): self
    {
        if ($this->skipSourceCodeUrls()) {
            $this->outputWarning(
                'Code source urls are not validated.'
                . ' Don\'t forget to remove --skip-source-code-urls'
                . ' parameter to validate it.'
            );
        } else {
            $expectedUrlIds = BenchmarkType::getSourceCodeUrlIds(
                ComponentConfiguration::getBenchmarkType(),
                ComponentConfiguration::getComponentType()
            );
            $urls = ComponentConfiguration::getSourceCodeUrls();
            foreach ($urls as $id => $url) {
                if (in_array($id, $expectedUrlIds) === false) {
                    $this->throwError('getSourceCodeUrls() return an array with unknown key "' . $id . '".');
                }

                $violations = static::validateSourCodeUrl($url);
                if (count($violations) > 0) {
                    $errors = [];
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                    $this->throwError(
                        'getSourceCodeUrls() value "'
                        . $url
                        . '" for key "'
                        . $id
                        . '" is not a valid url. '
                        . implode(', ', $errors)
                    );
                }
            }

            $missingIds = array_diff($expectedUrlIds, array_keys($urls));
            if (count($missingIds) > 0) {
                $this->throwError(
                    'getSourceCodeUrls() return an array with missing key'
                    . (count($missingIds) === 1 ? null : 's')
                    . ' '
                    . implode(', ', $missingIds)
                    . '.'
                );
            }

            $this->outputSuccess('getSourceCodeUrls() return valid urls.');
        }

        return $this;
    }
}
