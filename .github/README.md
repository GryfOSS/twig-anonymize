# GitHub Workflows

This repository includes comprehensive CI/CD workflows:

## Tests Workflow (`.github/workflows/tests.yml`)

### Triggers
- Push to `main` branch
- Tag creation (any tag)
- Pull requests to `main` branch

### Jobs

#### 1. Test Matrix
Tests across multiple PHP and Twig versions:
- **PHP Versions**: 8.1, 8.2, 8.3, 8.4
- **Twig Versions**: ^2.12, ^3.0
- **OS**: Ubuntu Latest

#### 2. What it does:
- ✅ Validates composer.json and composer.lock
- ✅ Installs dependencies with caching
- ✅ Runs PHPUnit tests
- ✅ Generates coverage reports (PHP 8.4 + Twig ^3.0 only)
- ✅ **Enforces 100% test coverage requirement**
- ✅ Runs Behat functional tests
- ✅ Uploads coverage to Codecov

#### 3. Code Quality Job
- ✅ PHP syntax validation
- ✅ Composer validation
- ✅ Security vulnerability checks

#### 4. Installation Test Job
- ✅ Tests fresh package installation
- ✅ Verifies the extension works in a clean environment

### Coverage Requirement
The workflow **fails if test coverage is not 100%**. This ensures:
- All code paths are tested
- No untested code is merged
- High code quality standards

### Usage

Add badges to your README:

```markdown
[![Tests](https://github.com/praetoriantechnology/twig-anonymize/actions/workflows/tests.yml/badge.svg)](https://github.com/praetoriantechnology/twig-anonymize/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/praetoriantechnology/twig-anonymize/branch/main/graph/badge.svg)](https://codecov.io/gh/praetoriantechnology/twig-anonymize)
```

### Local Testing

Run the same checks locally:

```bash
# Run all tests
composer test-all

# Check coverage
composer check-coverage

# Generate coverage report
composer test-coverage
```
