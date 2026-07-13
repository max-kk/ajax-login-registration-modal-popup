# Testing (PHPUnit)

## Unit tests

Run unit tests:

```bash
composer test
# or
composer test:unit
```

Unit tests are in `tests/unit` and use Brain Monkey to isolate WordPress functions.

## Integration tests scaffold

Run integration suite:

```bash
composer test:integration
```

Current integration suite is a scaffold (`tests/integration`) and will be skipped until WordPress core test suite is configured.

Set `WP_TESTS_DIR` to the local WordPress tests library path to enable integration execution.

## Notes

- PHPUnit config files:
  - `phpunit.xml.dist` (unit)
  - `phpunit.integration.xml.dist` (integration scaffold)
- Bootstrap files:
  - `tests/bootstrap.php`
  - `tests/integration/bootstrap.php`

Additional coverage:

- `tests/unit/RolesManagerTest.php` for `includes/class-roles-manager.php`
