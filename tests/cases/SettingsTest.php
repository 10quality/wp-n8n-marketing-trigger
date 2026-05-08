<?php

namespace N8nMarketingTrigger\Tests\Cases;

use N8nMarketingTrigger\Models\Settings;
use WP_UnitTestCase;

/**
 * Settings model tests.
 */
class SettingsTest extends WP_UnitTestCase
{
    /**
     * Tests helper returns singleton.
     */
    public function test_n8n_mt_settings_returns_singleton()
    {
        $first = n8n_mt_settings();
        $second = n8n_mt_settings();
        $this->assertInstanceOf( Settings::class, $first );
        $this->assertSame( $first, $second );
    }
    /**
     * Tests tabs and fields configuration.
     */
    public function test_settings_tabs_and_fields_exist()
    {
        $settings = n8n_mt_settings();
        $this->assertArrayHasKey( 'webhooks', $settings->tabs );
        $this->assertArrayHasKey( 'business', $settings->tabs );
        $this->assertArrayHasKey( 'payload', $settings->tabs );
        $this->assertArrayHasKey( 'method', $settings->tabs['webhooks']['fields'] );
        $this->assertArrayHasKey( 'business_name', $settings->tabs['business']['fields'] );
        $this->assertArrayHasKey( 'business_phone', $settings->tabs['business']['fields'] );
        $this->assertArrayHasKey( 'business_email', $settings->tabs['business']['fields'] );
        $this->assertArrayHasKey( 'business_description', $settings->tabs['business']['fields'] );
        $this->assertSame( 'POST', $settings->tabs['webhooks']['fields']['method']['default'] );
        $this->assertSame( 'Settings saved successfully', $settings->save_message );
    }
    /**
     * Tests payload view rendering.
     */
    public function test_render_payload_example_outputs_json_pre()
    {
        $settings = n8n_mt_settings();
        ob_start();
        $settings->render_payload_example( $settings );
        $html = ob_get_clean();
        $this->assertStringContainsString( '<pre ', $html );
        $this->assertStringContainsString( 'campaign_id', $html );
        $this->assertStringContainsString( 'settings', $html );
        $this->assertStringContainsString( 'call_to_action', $html );
        $this->assertStringContainsString( 'business', $html );
    }
}
