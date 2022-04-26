<?php

namespace Praetorian\Twig\Extension;

use InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AnonymizeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('anonymize', [$this, 'anonymize']),
        ];
    }

    public function anonymize(string $input, bool $keepLength = true, string $replacemenetChar = '*'): string
    {
        if (empty($input)) {
            return $input;
        }

        if (mb_strlen($replacemenetChar) !== 1) {
            throw new InvalidArgumentException('Replacement char must be exactly 1 character long string.');
        }

        $replacementLen = $keepLength ? max(mb_strlen($input) - 2, 3) : 3;
        return preg_replace('/(.)(.*)(.)/u', '$1' . str_repeat($replacemenetChar, $replacementLen) . '$3', $input);
    }
}