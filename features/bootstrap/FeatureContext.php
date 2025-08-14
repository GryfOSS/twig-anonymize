<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GryfOSS\Twig\Extension\AnonymizeExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Defines application features from the specific context.
 *
 * This context provides step definitions for testing the Twig anonymize filter
 * in a real Twig environment with actual template rendering.
 */
class FeatureContext implements Context
{
    private Environment $twig;
    private string $renderedOutput = '';
    private array $templates = [];
    private array $templateVariables = [];
    private ?\Exception $lastException = null;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->initializeTwig();
    }

    /**
     * Initialize Twig environment with our AnonymizeExtension.
     */
    private function initializeTwig(): void
    {
        $loader = new ArrayLoader([]);
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
            'strict_variables' => true,
        ]);

        // Add our AnonymizeExtension
        $this->twig->addExtension(new AnonymizeExtension());
    }

    /**
     * @Given I have a Twig template :templateName with content:
     */
    public function iHaveATwigTemplateWithContent(string $templateName, PyStringNode $content): void
    {
        $this->templates[$templateName] = $content->getRaw();

        // Update the Twig loader with all templates
        $loader = new ArrayLoader($this->templates);
        $this->twig->setLoader($loader);
    }

    /**
     * @Given I have template variable :variable with value :value
     */
    public function iHaveTemplateVariableWithValue(string $variable, string $value): void
    {
        // Try to decode JSON if the value looks like JSON
        if (str_starts_with($value, '[') || str_starts_with($value, '{')) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->templateVariables[$variable] = $decoded;
                return;
            }
        }

        $this->templateVariables[$variable] = $value;
    }

    /**
     * @Given I have template variables:
     */
    public function iHaveTemplateVariables(TableNode $table): void
    {
        foreach ($table->getRowsHash() as $variable => $value) {
            // Handle quoted strings by removing outer quotes
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $this->templateVariables[$variable] = $matches[1];
            } else {
                $this->iHaveTemplateVariableWithValue($variable, $value);
            }
        }
    }

    /**
     * @When I render the template :templateName
     */
    public function iRenderTheTemplate(string $templateName): void
    {
        try {
            $this->lastException = null;
            $template = $this->twig->load($templateName);
            $this->renderedOutput = $template->render($this->templateVariables);
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I render template :templateName with the anonymize filter
     */
    public function iRenderTemplateWithTheAnonymizeFilter(string $templateName): void
    {
        $this->iRenderTheTemplate($templateName);
    }

    /**
     * @Then the output should contain :text
     */
    public function theOutputShouldContain(string $text): void
    {
        if (strpos($this->renderedOutput, $text) === false) {
            throw new \Exception("Expected output to contain '{$text}', but got: {$this->renderedOutput}");
        }
    }

    /**
     * @Then the output should be exactly:
     */
    public function theOutputShouldBeExactly(PyStringNode $expectedOutput): void
    {
        $expected = trim($expectedOutput->getRaw());
        $actual = trim($this->renderedOutput);

        if ($expected !== $actual) {
            throw new \Exception("Expected exact output does not match actual output.\nExpected: '{$expected}'\nActual: '{$actual}'");
        }
    }

    /**
     * @Then the output should not contain :text
     */
    public function theOutputShouldNotContain(string $text): void
    {
        if (strpos($this->renderedOutput, $text) !== false) {
            throw new \Exception("Expected output to not contain '{$text}', but it was found in: {$this->renderedOutput}");
        }
    }

    /**
     * @Then the output should match pattern :pattern
     */
    public function theOutputShouldMatchPattern(string $pattern): void
    {
        if (!preg_match($pattern, $this->renderedOutput)) {
            throw new \Exception("Expected output to match pattern '{$pattern}', but got: {$this->renderedOutput}");
        }
    }

    /**
     * @Then an exception should be thrown with message :message
     */
    public function anExceptionShouldBeThrownWithMessage(string $message): void
    {
        if ($this->lastException === null) {
            throw new \Exception('Expected an exception to be thrown, but none was thrown');
        }

        if (strpos($this->lastException->getMessage(), $message) === false) {
            throw new \Exception("Expected exception message to contain '{$message}', but got: {$this->lastException->getMessage()}");
        }
    }

    /**
     * @Then no exception should be thrown
     */
    public function noExceptionShouldBeThrown(): void
    {
        if ($this->lastException !== null) {
            throw new \Exception(
                "Expected no exception to be thrown, but got: {$this->lastException->getMessage()}"
            );
        }
    }

    /**
     * @Then the anonymized :originalText should become :expectedResult
     */
    public function theAnonymizedShouldBecome(string $originalText, string $expectedResult): void
    {
        $this->iHaveTemplateVariableWithValue('text', $originalText);
        $this->iHaveATwigTemplateWithContent('test', new PyStringNode(['{{ text|anonymize }}'], 1));
        $this->iRenderTheTemplate('test');

        $actual = trim($this->renderedOutput);
        if ($expectedResult !== $actual) {
            throw new \Exception("Expected '{$originalText}' to be anonymized as '{$expectedResult}', but got: {$actual}");
        }
    }

    /**
     * @Then the output length should be :length
     */
    public function theOutputLengthShouldBe(int $length): void
    {
        $actualLength = mb_strlen(trim($this->renderedOutput));
        if ($length !== $actualLength) {
            throw new \Exception("Expected output length to be {$length}, but got {$actualLength}. Output: {$this->renderedOutput}");
        }
    }

    /**
     * @When I render template with anonymize filter using replacement char :char
     */
    public function iRenderTemplateWithAnonymizeFilterUsingReplacementChar(string $char): void
    {
        $template = "{{ text|anonymize(true, '{$char}') }}";
        $this->iHaveATwigTemplateWithContent('test', new PyStringNode([$template], 1));
        $this->iRenderTheTemplate('test');
    }

    /**
     * @When I render template with anonymize filter using keepLength :keepLength
     */
    public function iRenderTemplateWithAnonymizeFilterUsingKeepLength(string $keepLength): void
    {
        $keepLengthValue = $keepLength === 'true' ? 'true' : 'false';
        $template = "{{ text|anonymize({$keepLengthValue}) }}";
        $this->iHaveATwigTemplateWithContent('test', new PyStringNode([$template], 1));
        $this->iRenderTheTemplate('test');
    }

    /**
     * @Given I clear all template variables
     */
    public function iClearAllTemplateVariables(): void
    {
        $this->templateVariables = [];
    }

    /**
     * @Given I clear the rendered output
     */
    public function iClearTheRenderedOutput(): void
    {
        $this->renderedOutput = '';
        $this->lastException = null;
    }
}
