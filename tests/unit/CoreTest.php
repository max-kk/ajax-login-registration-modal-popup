<?php

declare(strict_types=1);

namespace LRM\Tests\Unit;

use Brain\Monkey\Functions;

if (!class_exists(__NAMESPACE__ . '\\DummyLrmPagesManager')) {
    class DummyLrmPagesManager
    {
        public static array $pages = [];

        public static function _get_pages_arr(): array
        {
            return self::$pages;
        }
    }
}

if (!class_exists('LRM_Pages_Manager', false)) {
    class_alias(__NAMESPACE__ . '\\DummyLrmPagesManager', 'LRM_Pages_Manager');
}

if (!class_exists('LRM_Core_TestDouble')) {
    class LRM_Core_TestDouble extends \LRM_Core
    {
        public array $renderArgs = [];

        public function __construct()
        {
        }

        public function render_form($from_inline = false, $default_tab = false, $role = false, $role_silent = false, $redirect_to = false)
        {
            $this->renderArgs = [$from_inline, $default_tab, $role, $role_silent, $redirect_to];
            echo 'FORM';
        }
    }
}

final class CoreTest extends TestCase
{
    protected function tearDown(): void
    {
        $_GET = [];
        parent::tearDown();
    }

    public function test_shortcode_returns_logged_in_message(): void
    {
        Functions\when('wp_parse_args')->alias(static function (array $atts, array $defaults): array {
            return array_merge($defaults, $atts);
        });
        Functions\when('is_customize_preview')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(true);
        Functions\when('esc_html')->alias(static fn(string $v): string => 'escaped:' . $v);

        $core = new LRM_Core_TestDouble();
        $result = $core->shortcode(['logged_in_message' => 'Hello']);

        $this->assertSame('escaped:Hello', $result);
        $this->assertSame([], $core->renderArgs);
    }

    public function test_shortcode_renders_form_for_guest_with_validated_redirect(): void
    {
        $_GET['redirect_to'] = rawurlencode('https://example.com/account');

        Functions\when('wp_parse_args')->alias(static function (array $atts, array $defaults): array {
            return array_merge($defaults, $atts);
        });
        Functions\when('is_customize_preview')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(false);
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('apply_filters')->justReturn('https://example.com/');
        Functions\expect('wp_validate_redirect')
            ->once()
            ->with('https://example.com/account', 'https://example.com/')
            ->andReturn('https://example.com/account');

        $core = new LRM_Core_TestDouble();
        $result = $core->shortcode(['default_tab' => 'register', 'role' => 'subscriber']);

        $this->assertSame('FORM', $result);
        $this->assertSame(
            [true, 'register', 'subscriber', false, 'https://example.com/account'],
            $core->renderArgs
        );
    }

    public function test_template_redirect_calls_wp_safe_redirect_for_lrm_page(): void
    {
        $_GET['redirect_to'] = '/dashboard';
        \LRM_Pages_Manager::$pages = [42 => 'login'];

        Functions\when('is_user_logged_in')->justReturn(true);
        Functions\when('get_the_ID')->justReturn(42);
        Functions\expect('wp_safe_redirect')->once()->with('/dashboard');

        $core = new LRM_Core_TestDouble();
        $core->template_redirect();
        $this->addToAssertionCount(1);
    }

    public function test_template_redirect_does_not_redirect_for_non_lrm_page(): void
    {
        $_GET['redirect_to'] = '/dashboard';
        \LRM_Pages_Manager::$pages = [42 => 'login'];

        Functions\when('is_user_logged_in')->justReturn(true);
        Functions\when('get_the_ID')->justReturn(7);
        Functions\expect('wp_safe_redirect')->never();

        $core = new LRM_Core_TestDouble();
        $core->template_redirect();
        $this->addToAssertionCount(1);
    }
}
