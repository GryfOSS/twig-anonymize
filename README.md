Twig Anonymize
==============

[![Tests](https://github.com/gryfoss/twig-anonymize/actions/workflows/tests.yml/badge.svg)](https://github.com/praetoriantechnology/twig-anonymize/actions/workflows/tests.yml)

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
composer require gryfoss/twig-anonymize
```

Since this is not a bundle for Symfony or any other framework but just a simple
filter you may need to tell your framework how to look for the filter.

In Symfony, in your `services.yaml`, add under `services`:
```yaml
    gryfoss.twig.anonymize_extension:
        class: GryfOSS\Twig\Extension\AnonymizeExtension
        tags:
            - { name: twig.extension }
```

Contributing
============

We welcome contributions from everyone! Whether you're fixing bugs, adding new features, or improving documentation, your help is appreciated.

## How to contribute:

- **🐛 Report issues**: Found a bug? [Open an issue](https://github.com/praetoriantechnology/twig-anonymize/issues/new) to let us know
- **💡 Feature requests**: Have an idea for improvement? [Create a feature request](https://github.com/praetoriantechnology/twig-anonymize/issues/new)
- **🔧 Pull requests**: Ready to contribute code? [Submit a pull request](https://github.com/praetoriantechnology/twig-anonymize/pulls)
- **📖 Documentation**: Help improve the documentation by submitting updates

## Development setup:

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/twig-anonymize.git`
3. Install dependencies: `composer install`
4. Run tests: `composer test-all`
5. Make your changes
6. Ensure 100% test coverage: `composer check-coverage`
7. Submit a pull request

All contributions must maintain **100% test coverage** and pass all quality checks. Our automated CI/CD pipeline will verify this when you submit your pull request.

Thank you for contributing! 🎉