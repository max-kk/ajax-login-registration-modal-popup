<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class EnvironmentTest extends TestCase
{
    public function test_wordpress_test_suite_is_configured(): void
    {
        $wpTestsDir = getenv('WP_TESTS_DIR');

        if (!$wpTestsDir || !is_dir($wpTestsDir)) {
            $this->markTestSkipped('Set WP_TESTS_DIR to run integration tests with WordPress core test suite.');
        }

        $this->assertDirectoryExists($wpTestsDir);
    }
}
