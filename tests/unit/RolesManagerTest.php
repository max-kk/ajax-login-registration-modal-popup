<?php

declare(strict_types=1);

namespace LRM\Tests\Unit;

use Brain\Monkey\Functions;

if (!class_exists('LRM_Field_Roles', false)) {
    class LRM_Field_Roles
    {
        public function input(): string
        {
            return '';
        }

        public function sanitize($value)
        {
            return $value;
        }
    }
}

final class RolesManagerTest extends TestCase
{
    public function test_get_wp_roles_flat_returns_translated_roles(): void
    {
        Functions\expect('get_editable_roles')
            ->once()
            ->andReturn([
                'subscriber' => ['name' => 'Subscriber'],
                'customer' => ['name' => 'Customer'],
            ]);

        Functions\expect('translate_user_role')
            ->twice()
            ->andReturnUsing(static function (string $name): string {
                return 'tr:' . $name;
            });

        $actual = \LRM_Roles_Manager::get_wp_roles_flat();

        $this->assertSame(
            [
                'subscriber' => 'tr:Subscriber',
                'customer' => 'tr:Customer',
            ],
            $actual
        );
    }

    public function test_register_settings_registers_expected_fields(): void
    {
        Functions\when('__')->alias(static fn(string $text): string => $text);
        Functions\when('esc_attr')->alias(static fn(string $value): string => $value);
        Functions\when('get_editable_roles')->justReturn([]);
        Functions\when('translate_user_role')->alias(static fn(string $name): string => $name);

        $settings = new RolesManagerSettingsStub();

        \LRM_Roles_Manager::register_settings($settings);

        $this->assertSame('user_role', $settings->lastSectionSlug);
        $this->assertSame('general', $settings->lastGroupSlug);
        $this->assertCount(3, $settings->fields);
        $this->assertSame(['on', 'active_roles', 'silent'], array_column($settings->fields, 'slug'));

        $activeRolesField = $settings->fields[1];
        $this->assertSame('active_roles', $activeRolesField['slug']);
        $this->assertArrayHasKey('addons', $activeRolesField);
        $this->assertArrayHasKey('options', $activeRolesField['addons']);
        $this->assertIsArray($activeRolesField['addons']['options']);
        $this->assertArrayHasKey('render', $activeRolesField);
        $this->assertArrayHasKey('sanitize', $activeRolesField);

        $this->assertStringContainsString(
            'Will work only with a PRO version installed',
            $settings->description
        );
    }
}

final class RolesManagerSettingsStub
{
    public string $lastSectionSlug = '';
    public string $lastGroupSlug = '';
    /** @var array<int, array<string, mixed>> */
    public array $fields = [];
    public string $description = '';

    public function add_section(string $title, string $slug): RolesManagerSectionStub
    {
        $this->lastSectionSlug = $slug;
        return new RolesManagerSectionStub($this);
    }
}

final class RolesManagerSectionStub
{
    public function __construct(private RolesManagerSettingsStub $settings)
    {
    }

    public function add_group(string $title, string $slug): RolesManagerGroupStub
    {
        $this->settings->lastGroupSlug = $slug;
        return new RolesManagerGroupStub($this->settings);
    }
}

final class RolesManagerGroupStub
{
    public function __construct(private RolesManagerSettingsStub $settings)
    {
    }

    /** @param array<string, mixed> $field */
    public function add_field(array $field): self
    {
        $this->settings->fields[] = $field;
        return $this;
    }

    public function description(string $description): self
    {
        $this->settings->description = $description;
        return $this;
    }
}
