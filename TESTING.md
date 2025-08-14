# Twig Anonymize Extension - Testing

This project includes **100% unit test coverage** for the AnonymizeExtension with comprehensive PHPDoc documentation and modern PHPUnit 11 features.

## Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage
```

## Modern PHPUnit Features

This test suite uses modern PHPUnit 11 features including:

- **PHP 8 Attributes**: Uses `#[DataProvider]` instead of deprecated `@dataProvider` annotations
- **Type Declarations**: Full type hints for all test methods and data providers
- **Latest PHPUnit**: Compatible with PHPUnit 11.5+ with no deprecation warnings
- **Modern Configuration**: Uses the latest phpunit.xml schema

## Documentation

The codebase includes comprehensive PHPDoc documentation for:

### Main Extension Class
- **Class-level documentation**: Explains the extension's purpose and behavior patterns
- **Method documentation**: Detailed explanations of `getFilters()` and `anonymize()` methods
- **Parameter documentation**: Complete type hints and descriptions for all parameters
- **Return value documentation**: Clear descriptions of return types and values
- **Exception documentation**: Details about when and why exceptions are thrown
- **Usage examples**: Practical examples showing different anonymization scenarios

### Test Class
- **Class-level documentation**: Overview of the test suite's purpose and scope
- **Method documentation**: Descriptions of key test methods and their objectives
- **Data provider documentation**: Comprehensive explanation of test data structure and coverage
- **Modern attributes**: Uses PHP 8 attributes for cleaner, more maintainable test code

## Test Coverage

The test suite covers all functionality including:

### Core Functionality
- **Length < 3**: Full string replacement
- **Length == 3**: First character + replacement
- **Length > 3**: First + replacement + last character

### Edge Cases
- Empty strings
- Unicode characters (including emojis)
- Special characters
- Whitespace handling
- Custom replacement characters

### Error Handling
- Invalid replacement character validation
- Empty replacement character
- Multi-character replacement character

### Parameters
- `keepLength` parameter behavior
- Custom replacement characters

## Test Statistics

- **57 tests** with **82 assertions**
- **100% code coverage** (Classes: 1/1, Methods: 2/2, Lines: 16/16)
- Tests all three behavior patterns based on string length
- Comprehensive Unicode support testing
- Full exception handling coverage

## Anonymization Behavior

| Input Length | Behavior | Example |
|--------------|----------|---------|
| < 3 | Full replacement | `"hi"` → `"**"` |
| == 3 | First + replacement | `"cat"` → `"c**"` |
| > 3 | First + replacement + last | `"hello"` → `"h***o"` |

The filter properly handles Unicode characters and validates that replacement characters are exactly one character long.
