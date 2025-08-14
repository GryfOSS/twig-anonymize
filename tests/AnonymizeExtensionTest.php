<?php

namespace GryfOSS\Twig\Extension\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use GryfOSS\Twig\Extension\AnonymizeExtension;
use Twig\TwigFilter;

/**
 * Test suite for AnonymizeExtension.
 *
 * Provides comprehensive testing of the anonymize filter functionality
 * including edge cases, Unicode support, and error handling.
 */
class AnonymizeExtensionTest extends TestCase
{
    private AnonymizeExtension $extension;

    /**
     * Set up test environment before each test.
     */
    protected function setUp(): void
    {
        $this->extension = new AnonymizeExtension();
    }

    /**
     * Test that the extension provides the correct filters.
     */
    public function testGetFilters(): void
    {
        $filters = $this->extension->getFilters();

        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(TwigFilter::class, $filters[0]);
        $this->assertEquals('anonymize', $filters[0]->getName());

        // Test that the filter is callable
        $callable = $filters[0]->getCallable();
        $this->assertIsCallable($callable);
        $this->assertEquals([$this->extension, 'anonymize'], $callable);
    }

    /**
     * Test anonymization of empty strings.
     */
    public function testAnonymizeWithEmptyString(): void
    {
        $result = $this->extension->anonymize('');
        $this->assertEquals('', $result);
    }

    // Test length < 3: full string gets replaced
    /**
     * Test anonymization behavior for single character strings.
     */
    public function testAnonymizeWithSingleCharacter(): void
    {
        $result = $this->extension->anonymize('a');
        $this->assertEquals('*', $result);
    }

    public function testAnonymizeWithTwoCharacters(): void
    {
        $result = $this->extension->anonymize('ab');
        $this->assertEquals('**', $result);
    }

    public function testAnonymizeWithSingleCharacterKeepLengthFalse(): void
    {
        $result = $this->extension->anonymize('a', false);
        $this->assertEquals('*', $result);
    }

    public function testAnonymizeWithTwoCharactersKeepLengthFalse(): void
    {
        $result = $this->extension->anonymize('ab', false);
        $this->assertEquals('**', $result);
    }

    // Test length == 3: first character remains, rest gets replaced
    public function testAnonymizeWithThreeCharacters(): void
    {
        $result = $this->extension->anonymize('abc');
        $this->assertEquals('a**', $result);
    }

    public function testAnonymizeWithThreeCharactersKeepLengthFalse(): void
    {
        $result = $this->extension->anonymize('abc', false);
        $this->assertEquals('a**', $result);
    }

    // Test length > 3: first and last character remain, rest gets replaced
    public function testAnonymizeWithFourCharacters(): void
    {
        $result = $this->extension->anonymize('abcd');
        $this->assertEquals('a**d', $result);
    }

    public function testAnonymizeWithFourCharactersKeepLengthFalse(): void
    {
        $result = $this->extension->anonymize('abcd', false);
        $this->assertEquals('a***d', $result);
    }

    public function testAnonymizeWithLongString(): void
    {
        $result = $this->extension->anonymize('hello world');
        $this->assertEquals('h*********d', $result);
    }

    public function testAnonymizeWithLongStringKeepLengthFalse(): void
    {
        $result = $this->extension->anonymize('hello world', false);
        $this->assertEquals('h***d', $result);
    }

    public function testAnonymizeWithKeepLengthTrue(): void
    {
        $input = 'testing'; // 7 characters
        $result = $this->extension->anonymize($input, true);
        $this->assertEquals('t*****g', $result);
        $this->assertEquals(mb_strlen($input), mb_strlen($result));
    }

    public function testAnonymizeWithKeepLengthFalse(): void
    {
        $input = 'testing'; // 7 characters
        $result = $this->extension->anonymize($input, false);
        $this->assertEquals('t***g', $result);
        $this->assertNotEquals(mb_strlen($input), mb_strlen($result));
        $this->assertEquals(5, mb_strlen($result));
    }

    public function testAnonymizeWithCustomReplacementChar(): void
    {
        $result = $this->extension->anonymize('hello', true, '#');
        $this->assertEquals('h###o', $result);
    }

    public function testAnonymizeParameterTypo(): void
    {
        // Test that the misspelled parameter name still works
        $result = $this->extension->anonymize('test', true, '@');
        $this->assertEquals('t@@t', $result);
    }

    // Unicode tests
    public function testAnonymizeWithUnicodeString(): void
    {
        $result = $this->extension->anonymize('тест', true, '*'); // 4 characters
        $this->assertEquals('т**т', $result);
    }

    public function testAnonymizeWithUnicodeReplacementChar(): void
    {
        $result = $this->extension->anonymize('hello', true, '●');
        $this->assertEquals('h●●●o', $result);
    }

    public function testAnonymizeWithEmojis(): void
    {
        $result = $this->extension->anonymize('🚀test🎉', true, '*'); // 6 characters
        $this->assertEquals('🚀****🎉', $result);
    }

    public function testAnonymizeWithUnicodeSingleChar(): void
    {
        $result = $this->extension->anonymize('ñ', true, '*');
        $this->assertEquals('*', $result);
    }

    public function testAnonymizeWithUnicodeTwoChars(): void
    {
        $result = $this->extension->anonymize('ñó', true, '*');
        $this->assertEquals('**', $result);
    }

    public function testAnonymizeWithUnicodeThreeChars(): void
    {
        $result = $this->extension->anonymize('café', true, '*'); // 4 characters
        $this->assertEquals('c**é', $result);
    }

    public function testAnonymizeWithSpecialCharacters(): void
    {
        $result = $this->extension->anonymize('!@#$%', true, '*'); // 5 characters
        $this->assertEquals('!***%', $result);
    }

    // Exception tests
    public function testAnonymizeThrowsExceptionForEmptyReplacementChar(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Replacement char must be exactly 1 character long string.');

        $this->extension->anonymize('test', true, '');
    }

    public function testAnonymizeThrowsExceptionForMultiCharacterReplacement(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Replacement char must be exactly 1 character long string.');

        $this->extension->anonymize('test', true, '**');
    }

    public function testAnonymizeThrowsExceptionForMultiByteReplacementChar(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Replacement char must be exactly 1 character long string.');

        $this->extension->anonymize('test', true, 'ab');
    }

    public function testAnonymizeThrowsExceptionForUnicodeMultiChar(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Replacement char must be exactly 1 character long string.');

        $this->extension->anonymize('test', true, 'ñó');
    }

    /**
     * Test anonymization with various inputs using data provider.
     *
     * @param string $input The input string to anonymize
     * @param bool $keepLength Whether to keep the original length
     * @param string $replacementChar The replacement character to use
     * @param string $expected The expected anonymized result
     */
    #[DataProvider('anonymizeDataProvider')]
    public function testAnonymizeWithDataProvider(
        string $input,
        bool $keepLength,
        string $replacementChar,
        string $expected
    ): void {
        $result = $this->extension->anonymize($input, $keepLength, $replacementChar);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for anonymization test cases.
     *
     * Provides comprehensive test data covering all anonymization scenarios:
     * - Different string lengths (< 3, = 3, > 3)
     * - Various keepLength settings
     * - Different replacement characters
     * - Unicode and emoji support
     * - Special characters and edge cases
     *
     * Used with the #[DataProvider] attribute for modern PHPUnit testing.
     *
     * @return array<string, array{string, bool, string, string}> Test case data
     */
    public static function anonymizeDataProvider(): array
    {
        return [
            // Empty string
            'Empty string' => ['', true, '*', ''],

            // Length < 3: full replacement
            'Single char with keep length' => ['a', true, '*', '*'],
            'Single char without keep length' => ['a', false, '*', '*'],
            'Two chars with keep length' => ['ab', true, '*', '**'],
            'Two chars without keep length' => ['ab', false, '*', '**'],

            // Length == 3: first char + replacement
            'Three chars with keep length' => ['abc', true, '*', 'a**'],
            'Three chars without keep length' => ['abc', false, '*', 'a**'],

            // Length > 3: first + replacement + last
            'Four chars with keep length' => ['abcd', true, '*', 'a**d'],
            'Four chars without keep length' => ['abcd', false, '*', 'a***d'],
            'Five chars with keep length' => ['abcde', true, '*', 'a***e'],
            'Five chars without keep length' => ['abcde', false, '*', 'a***e'],
            'Long string with keep length' => ['hello world', true, '*', 'h*********d'],
            'Long string without keep length' => ['hello world', false, '*', 'h***d'],

            // Custom replacement char
            'Custom replacement char' => ['test', true, '#', 't##t'],

            // Unicode strings
            'Unicode single char' => ['ñ', true, '*', '*'],
            'Unicode two chars' => ['ñó', true, '*', '**'],
            'Unicode three chars' => ['ñóü', true, '*', 'ñ**'],
            'Unicode four chars' => ['café', true, '•', 'c••é'],
            'Unicode long string' => ['привет', true, '*', 'п****т'],

            // Numbers and special chars
            'Numbers' => ['12345', true, 'X', '1XXX5'],
            'Special chars' => ['!@#$%', true, '-', '!---%'],
            'Mixed content' => ['a1!b2@c', true, '_', 'a_____c'],

            // Whitespace
            'Whitespace only' => ['   ', true, '*', ' **'],
            'With spaces' => ['a b c d e', true, '_', 'a_______e'],

            // Emojis
            'Emoji single' => ['🚀', true, '*', '*'],
            'Emoji two' => ['🚀🎉', true, '*', '**'],
            'Emoji three' => ['🚀🎉✨', true, '*', '🚀**'],
            'Emoji in text' => ['🚀test🎉', true, '*', '🚀****🎉'],
        ];
    }

    public function testAnonymizeEdgeCases(): void
    {
        // Test with various whitespace
        $result = $this->extension->anonymize(' ', true, '*');
        $this->assertEquals('*', $result);

        $result = $this->extension->anonymize('  ', true, '*');
        $this->assertEquals('**', $result);

        $result = $this->extension->anonymize('   ', true, '*');
        $this->assertEquals(' **', $result);

        // Test with newlines and tabs
        $result = $this->extension->anonymize("a\nb", true, '*');
        $this->assertEquals('a**', $result); // 3 chars: first + replacement

        $result = $this->extension->anonymize("a\n\tb", true, '*');
        $this->assertEquals('a**b', $result); // 4 chars: first+last
    }

    public function testAnonymizeBehaviorByLength(): void
    {
        // Verify the three different behaviors based on length

        // Length < 3: full replacement
        for ($i = 1; $i <= 2; $i++) {
            $input = str_repeat('a', $i);
            $result = $this->extension->anonymize($input, true, '*');
            $this->assertEquals(str_repeat('*', $i), $result, "Failed for length $i");
        }

        // Length == 3: first + replacement
        $result = $this->extension->anonymize('abc', true, '*');
        $this->assertEquals('a**', $result);

        // Length > 3: first + replacement + last
        for ($i = 4; $i <= 10; $i++) {
            $input = str_repeat('a', $i - 1) . 'z'; // 'aaa...z'
            $result = $this->extension->anonymize($input, true, '*');
            $expected = 'a' . str_repeat('*', $i - 2) . 'z';
            $this->assertEquals($expected, $result, "Failed for length $i");
        }
    }
}
