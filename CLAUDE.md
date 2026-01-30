# CLAUDE.md

Instructions for Claude Code when working with this Symfony bundle.

## Commands

```bash
# Tests
composer test                              # Run all tests
vendor/bin/phpunit tests/Unit/...          # Run specific test

# Code Quality
composer phpstan                           # PHPStan (level 7)
/opt/homebrew/bin/php-cs-fixer fix         # Fix code style
```

## Code Style Decisions

- **Strict types:** Required in all PHP files (`declare(strict_types=1);`)
- **Indentation:** 4 spaces (PSR-12)
- **File headers:** Automatically added by PHP-CS-Fixer (no manual work needed)
- **PHPDoc:** Required for public methods, especially array type hints

## Testing

- Unit tests: `tests/Unit/`
- Integration tests: `tests/Integration/`
- Standards tests: `tests/Standards/` (license headers, docblocks)

## Key Decisions

- Route options take precedence over `#[Sitemap]`/`#[Robots]` attributes
- Route options for sitemap/robots are deprecated (use attributes)
- All exceptions extend `_SitemapException`
- CHANGELOG.md is updated via `bin/release.php`
