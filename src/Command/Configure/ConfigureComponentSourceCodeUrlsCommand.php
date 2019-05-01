<?php

declare(strict_types=1);

namespace App\Command\Configure;

use AbstractComponentConfiguration\AbstractComponentConfiguration;
use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Validate\ValidateConfigurationComponentSourceCodeUrlsCommand,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Validator\ConstraintViolationInterface;

class ConfigureComponentSourceCodeUrlsCommand extends AbstractConfigureCommand
{
    use DefineVariableTrait;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('configure:component:sourceCodeUrls')
            ->setDescription(
                'Create .phpbenchmarks/AbstractComponentConfiguration.php and configure getSourceCodeUrls()'
            )
            ->addOption('skip-component-creation', null, null, 'Skip component creation ()');
    }

    protected function doExecute(): AbstractCommand
    {
        if ($this->getInput()->getOption('skip-component-creation') === false) {
            $this->runCommand('configure:component');
        }

        if ($this->skipSourceCodeUrls() === false) {
            $this
                ->title('Configuration of AbstractComponentConfiguration::getSourceCodeUrls()')
                ->defineSourceCodeUrls();
        }

        return $this;
    }

    protected function defineSourceCodeUrls(): self
    {
        $reflection = new \ReflectionClass(AbstractComponentConfiguration::class);
        $method = $reflection->getMethod('getSourceCodeUrls');

        $classCode = file($reflection->getFileName());
        for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {
            unset($classCode[$i]);
        }
        unset($classCode[$reflection->getEndLine() - 1]);

        $getSourceCodeUrls = file($this->getTypedDefaultConfigurationPath() . '/getSourceCodeUrls.tpl');

        file_put_contents(
            $reflection->getFileName(),
            implode(null, array_merge($classCode, $getSourceCodeUrls, ['}']))
        );

        foreach ($this->getSourceCodeUrls() as $url) {
            $this->defineVariable(
                $url['variable'],
                function () use ($url) {
                    return $url['url'];
                },
                $this->getConfigurationPath() . '/AbstractComponentConfiguration.php'
            );
        }

        return $this;
    }

    protected function getSourceCodeUrls(): array
    {
        $return = $this->getSourceCodeUrlsToAsk(
            ComponentConfiguration::getBenchmarkType(),
            ComponentConfiguration::getComponentType(),
            ComponentConfiguration::getSourceCodeUrls()
        );

        foreach ($return as &$url) {
            $violations = ValidateConfigurationComponentSourceCodeUrlsCommand::validateSourCodeUrl($url['url']);
            $showWarning = false;
            do {
                if (count($violations) > 0 && $showWarning) {
                    $errors = [];
                    /** @var ConstraintViolationInterface $violation */
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                    $this->warning(implode(', ', $errors));
                }

                $url['url'] = $this->question(
                    $url['question'],
                    substr($url['url'] ?? '', 0, 4) === '____' ? null : $url['url']
                );

                $showWarning = true;
            } while (
                count(
                    $violations = ValidateConfigurationComponentSourceCodeUrlsCommand::validateSourCodeUrl($url['url'])
                ) > 0
            );
        }

        return $return;
    }

    protected function getSourceCodeUrlsToAsk(int $benchmarkType, int $componentType, array $defaultUrls): array
    {
        $availableUrls = [
            'entryPoint' => [
                'question' => 'URL to entry point code?',
                'variable' => '____PHPBENCHMARKS_ENTRY_POINT_URL____'
            ],
            'template' => [
                'question' => 'URL to template code?',
                'variable' => '____PHPBENCHMARKS_TEMPLATE_URL____'
            ],
            'route' => [
                'question' => 'URL to benchmark route code?',
                'variable' => '____PHPBENCHMARKS_ROUTE_SOURCE_CODE_URL____'
            ],
            'controller' => [
                'question' => 'URL to Controller code?',
                'variable' => '____PHPBENCHMARKS_CONTROLLER_SOURCE_CODE_URL____'
            ],
            'randomizeLanguageDispatchEvent' => [
                'question' => 'URL to code who dispatch event to randomize language?',
                'variable' => '____PHPBENCHMARKS_RANDOMIZE_LANGUAGE_DISPATCH_EVENT_SOURCE_CODE_URL____'
            ],
            'randomizeLanguageEventListener' => [
                'question' => 'URL to code who listen event to randomize language?',
                'variable' => '____PHPBENCHMARKS_RANDOMIZE_LANGUAGE_EVENT_LISTENER_SOURCE_CODE_URL____'
            ],
            'translations' => [
                'question' => 'URL to en_GB translations code?',
                'variable' => '____PHPBENCHMARKS_TRANSLATIONS_SOURCE_CODE_URL____'
            ],
            'translate' => [
                'question' => 'URL to code who translate translated.1000 key?',
                'variable' => '____PHPBENCHMARKS_TRANSLATE_SOURCE_CODE_URL____'
            ],
            'serialize' => [
                'question' => 'URL to code who serialize User?',
                'variable' => '____PHPBENCHMARKS_SERIALIZE_SOURCE_CODE_URL____'
            ]
        ];

        $return = [];
        foreach (BenchmarkType::getSourceCodeUrlIds($benchmarkType, $componentType) as $urlId) {
            if (array_key_exists($urlId, $availableUrls) === false) {
                throw new \Exception('Unknown url id "' . $urlId . '".');
            }
            $return[$urlId] = array_merge($availableUrls[$urlId], ['url' => $defaultUrls[$urlId] ?? null]);
        }

        return $return;
    }
}
