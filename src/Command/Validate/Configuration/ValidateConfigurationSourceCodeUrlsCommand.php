<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Command\Configure\ConfigureSourceCodeUrlsCommand,
    Utils\Path
};
use Symfony\Component\Validator\{
    Constraints\NotBlank,
    Constraints\Type,
    Constraints\Url,
    ConstraintViolationListInterface,
    Validation
};

final class ValidateConfigurationSourceCodeUrlsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:source-code-urls';

    public static function validateUrl($url): ConstraintViolationListInterface
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

        $this->setDescription('Validate sourceCode.urls in ' . Path::rmPrefix(Path::getConfigFilePath()));
    }

    protected function doExecute(): int
    {
        if ($this->skipSourceCodeUrls() === false) {
            $this
                ->outputTitle('Validation of sourceCode.urls in ' . Path::rmPrefix(Path::getConfigFilePath()))
                ->assertCodeSourceUrls();
        }

        return 0;
    }

    protected function onError(): parent
    {
        return $this
            ->outputWarning(
                'You can call "phpbenchkit '
                    . ConfigureSourceCodeUrlsCommand::getDefaultName()
                    . '" to configure it.'
            )
            ->outputWarning('You can add --skip-source-code-urls parameter to skip this validation.');
    }

    private function assertCodeSourceUrls(): self
    {
        $expectedUrlIds = BenchmarkType::getSourceCodeUrlIds();

        $urls = Benchmark::getSourceCodeUrls();
        foreach ($urls as $id => $url) {
            if (in_array($id, $expectedUrlIds, true) === false) {
                throw new \Exception('Configuration sourceCode.urls contains an unknown key "' . $id . '".');
            }

            $violations = static::validateUrl($url);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                throw new \Exception(
                    'Url "'
                        . $url
                        . '" for key "'
                        . $id
                        . '" is not a valid url. '
                        . implode(', ', $errors)
                );
            }
        }

        $missingIds = array_diff($expectedUrlIds, array_keys($urls->toArray()));
        if (count($missingIds) > 0) {
            throw new \Exception(
                'Missing key'
                    . (count($missingIds) === 1 ? null : 's')
                    . ' '
                    . implode(', ', $missingIds)
                    . ' in configuration sourceCode.urls.'
            );
        }

        $this->outputSuccess('Configuration sourceCode.urls is valid.');

        return $this;
    }
}
