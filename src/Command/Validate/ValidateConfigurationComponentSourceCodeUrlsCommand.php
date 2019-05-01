<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Validator\{
    Constraints\NotBlank,
    Constraints\Type,
    Constraints\Url,
    ConstraintViolationListInterface,
    Validation
};

class ValidateConfigurationComponentSourceCodeUrlsCommand extends AbstractCommand
{
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

        $this
            ->setName('validate:configuration:component:sourceCodeUrls')
            ->setDescription('Validate .phpbenchmarks/AbstractComponentConfiguration.php::getSourceCodeUrls()');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Validation of .phpbenchmarks/AbstractComponentConfiguration.php::getSourceCodeUrls()')
            ->assertCodeSourceUrls();

        return $this;
    }

    protected function onError(): parent
    {
        $this
            ->warning('You can call "phpbench configure:component:sourceCodeUrls" to configure it.')
            ->warning('You can add --skip-source-code-urls parameter to skip this validation.');

        return $this;
    }

    protected function assertCodeSourceUrls(): self
    {
        if ($this->skipSourceCodeUrls()) {
            $this->warning(
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
                    $this->error('getSourceCodeUrls() return an array with unknown key "' . $id . '".');
                }

                $violations = static::validateSourCodeUrl($url);
                if (count($violations) > 0) {
                    $errors = [];
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                    $this->error(
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
                $this->error(
                    'getSourceCodeUrls() return an array missing the key'
                    . (count($missingIds) === 1 ? null : 's')
                    . ' '
                    . implode(', ', $missingIds)
                    . '.'
                );
            }

            $this->success('getSourceCodeUrls() return valid urls.');
        }

        return $this;
    }
}
