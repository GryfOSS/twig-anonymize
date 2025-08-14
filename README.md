Twig Anonymize
==============

Basic Twig Anonymize extension which allows you to anonymize texts in your twig
frontend. It keeps first and last letter (if string longer than 2) and replaces the
rest with symbols (asterisks by default).
.
If you have a variable called `mytext` then you can anonymize it by typing:
```twig
{{ mytext|anonymize }}
```

Filter has few options:
* keepLength: determines if original lenght of the text should be left intact or
should it minify the hidden part.
* replacementChar: character to be used in for the replacement. Defaults to *.

For example:
```twig
{{ mytext|anonymize(false, '!') }}
```
will not keep original length (replace with 3 symbols) and use '!' as the replacement
char.

Warning: replacement length is always at least 3 symbols long, no matter the
`keepLength` setting.

Installation
============

Install using composer:
```bash
composer require praetoriantechnology/twig-anonymize:dev-main
```

**NOTE**: The `dev-main` references to the main branch, until there is a stable
version that is required.

Since this is not a bundle for Symfony or any other framework but just a simple
filter you may need to tell your framework how to look for the filter.

In Symfony, in your `services.yaml`, add under `services`:
```yaml
    gryfoss.twig.anonymize_extension:
        class: GryfOSS\Twig\Extension\AnonymizeExtension
        tags:
            - { name: twig.extension }
```