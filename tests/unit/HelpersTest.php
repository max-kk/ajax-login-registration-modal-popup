<?php

declare(strict_types=1);

namespace LRM\Tests\Unit;

use Brain\Monkey\Functions;
use Mockery;

if (!class_exists(__NAMESPACE__ . '\\DummyLrmPro')) {
    class DummyLrmPro
    {
    }
}

final class HelpersTest extends TestCase
{
    public function test_lrm_is_pro_returns_false_when_pro_is_not_loaded(): void
    {
        $this->assertFalse(lrm_is_pro());
        $this->assertFalse(lrm_is_pro('1.0'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_lrm_is_pro_compares_required_version(): void
    {
        if (!class_exists('LRM_Pro', false)) {
            class_alias(__NAMESPACE__ . '\\DummyLrmPro', 'LRM_Pro');
        }

        if (!defined('LRM_PRO_VERSION')) {
            define('LRM_PRO_VERSION', '2.0');
        }

        $this->assertTrue(lrm_is_pro());
        $this->assertTrue(lrm_is_pro('1.9'));
        $this->assertFalse(lrm_is_pro('2.1'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_lrm_wc_version_uses_wc_version_constant(): void
    {
        define('WC_VERSION', '8.8.1');

        $this->assertTrue(lrm_wc_version('8.8.0'));
        $this->assertFalse(lrm_wc_version('9.0.0'));
        $this->assertTrue(lrm_wc_version('8.8.1', '=='));
    }

    public function test_lrm_log_dispatches_plain_logger_action(): void
    {
        Functions\expect('do_action')
            ->once()
            ->with(
                'plain_logger',
                'Security event',
                Mockery::on(static function ($data): bool {
                    return is_string($data) && strpos($data, '[attempts] => 2') !== false;
                })
            );

        lrm_log('Security event', ['attempts' => 2]);

        $this->addToAssertionCount(1);
    }
}
