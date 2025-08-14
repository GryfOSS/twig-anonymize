<?php

namespace GryfOSS\Twig\Extension;

use InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension that provides an anonymize filter for string obfuscation.
 *
 * This extension adds an 'anonymize' filter that replaces characters in a string
 * while preserving certain characters based on string length:
 * - Length < 3: Full string replacement
 * - Length = 3: First character preserved, rest replaced
 * - Length > 3: First and last characters preserved, middle replaced
 */
class AnonymizeExtension extends AbstractExtension
{
    /**
     * Returns the list of filters provided by this extension.
     *
     * @return TwigFilter[] An array of TwigFilter instances
     */
    public function getFilters()
    {
        return [
            new TwigFilter('anonymize', [$this, 'anonymize']),
        ];
    }

    /**
     * Anonymizes a string by replacing characters while preserving structure.
     *
     * The anonymization behavior depends on the input string length:
     * - Length < 3: Full string gets replaced by the replacement character
     * - Length = 3: First character remains original, rest gets replaced
     * - Length > 3: First and last character remain original, rest gets replaced
     *
     * @param string $input The string to anonymize
     * @param bool $keepLength Whether to maintain the original string length (default: true)
     * @param string $replacemenetChar The character to use for replacement (default: '*')
     *
     * @return string The anonymized string
     *
     * @throws InvalidArgumentException If the replacement character is not exactly 1 character long
     *
     * @example
     * anonymize('a')        // '*'
     * anonymize('hi')       // '**'
     * anonymize('cat')      // 'c**'
     * anonymize('hello')    // 'h***o'
     * anonymize('test', false, '#')  // 't###t'
     */
    public function anonymize(string $input, bool $keepLength = true, string $replacemenetChar = '*'): string
    {
        if (empty($input)) {
            return $input;
        }

        if (mb_strlen($replacemenetChar) !== 1) {
            throw new InvalidArgumentException('Replacement char must be exactly 1 character long string.');
        }

        $length = mb_strlen($input);

        if ($length < 3) {
            // Full string gets replaced by the replacement character
            $replacementLen = $keepLength ? $length : $length;
            return str_repeat($replacemenetChar, $replacementLen);
        } elseif ($length === 3) {
            // First character remains original, rest gets replaced
            $replacementLen = $keepLength ? 2 : 2;
            return mb_substr($input, 0, 1) . str_repeat($replacemenetChar, $replacementLen);
        } else {
            // First and last character remain original, rest gets replaced
            $replacementLen = $keepLength ? $length - 2 : 3;
            return mb_substr($input, 0, 1) . str_repeat($replacemenetChar, $replacementLen) . mb_substr($input, -1);
        }
    }
}