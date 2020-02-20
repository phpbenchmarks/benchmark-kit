<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigSourceCodeUrlsCommand,
    Benchmark\Benchmark,
    Utils\Path
};
use Symfony\Component\Validator\{
    Constraints\NotBlank,
    Constraints\Type,
    Constraints\Url,
    ConstraintViolationListInterface,
    Validation
};

final class ValidatePhpBenchmarksConfigSourceCodeUrlsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpBenchmarks:config:sourceCodeUrls';

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

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getConfigFilePath()) . ' sourceCode.urls');
    }

    protected function doExecute(): int
    {
        if ($this->skipSourceCodeUrls()) {
            return $this;
        }

        $this
            ->outputTitle('Validation of ' . Path::rmPrefix(Path::getConfigFilePath()) . ' sourceCode.urls')
            ->assertCodeSourceUrls();

        return 0;
    }

    protected function onError(): parent
    {
        return $this
            ->outputWarning(
                'You can call "phpbenchkit '
                    . ConfigurePhpBenchmarksConfigSourceCodeUrlsCommand::getDefaultName()
                    . '" to configure it.'
            )
            ->outputWarning('You can add --skip-source-code-urls parameter to skip this validation.');
    }

    private function assertCodeSourceUrls(): self
    {
        $expectedUrlIds = BenchmarkType::getSourceCodeUrlIds();

        $urls = Benchmark::getSourceCodeUrls();
        foreach ($urls as $id => $url) {
            if (in_array($id, $expectedUrlIds) === false) {
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
