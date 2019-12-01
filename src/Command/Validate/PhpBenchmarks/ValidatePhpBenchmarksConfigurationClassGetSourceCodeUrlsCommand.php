<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigurationClassGetSourceCodeUrlsCommand,
    ComponentConfiguration\ComponentConfiguration,
    Utils\Path
};
use Symfony\Component\Validator\{
    Constraints\NotBlank,
    Constraints\Type,
    Constraints\Url,
    ConstraintViolationListInterface,
    Validation
};

final class ValidatePhpBenchmarksConfigurationClassGetSourceCodeUrlsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpBenchmarks:configurationClass:getSourceCodeUrls';

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

        $this->setDescription(
            'Validate '
                . Path::rmPrefix(Path::getBenchmarkConfigurationClassPath())
                . '::getSourceCodeUrls()'
        );
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle(
                'Validation of '
                    . Path::rmPrefix(Path::getBenchmarkConfigurationClassPath())
                    . '::getSourceCodeUrls()'
            )
            ->assertCodeSourceUrls();
    }

    protected function onError(): parent
    {
        return $this
            ->outputWarning(
                'You can call "phpbenchkit '
                    . ConfigurePhpBenchmarksConfigurationClassGetSourceCodeUrlsCommand::getDefaultName()
                    . '" to configure it.'
            )
            ->outputWarning('You can add --skip-source-code-urls parameter to skip this validation.');
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
                    throw new \Exception('getSourceCodeUrls() return an array with unknown key "' . $id . '".');
                }

                $violations = static::validateSourCodeUrl($url);
                if (count($violations) > 0) {
                    $errors = [];
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                    throw new \Exception(
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
                throw new \Exception(
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
