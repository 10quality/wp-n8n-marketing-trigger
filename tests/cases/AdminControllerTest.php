<?php

namespace N8nMarketingTrigger\Tests\Cases;

use N8nMarketingTrigger\Controllers\AdminController;
use WP_UnitTestCase;

/**
 * Admin controller tests.
 */
class AdminControllerTest extends WP_UnitTestCase
{
    /**
     * Cleanup globals.
     */
    protected function tearDown(): void
    {
        unset( $_GET['n8n_mt_notice'] );
        parent::tearDown();
    }
    /**
     * Tests success notice rendering.
     */
    public function test_campaign_notices_renders_success_notice()
    {
        $_GET['n8n_mt_notice'] = 'test_sent';
        $controller = new AdminController;
        ob_start();
        $controller->campaign_notices();
        $html = ob_get_clean();
        $this->assertStringContainsString( 'notice-success', $html );
        $this->assertStringContainsString( 'Test campaign sent successfully', $html );
    }
    /**
     * Tests error notice rendering.
     */
    public function test_campaign_notices_renders_error_notice()
    {
        $_GET['n8n_mt_notice'] = 'send_failed';
        $controller = new AdminController;
        ob_start();
        $controller->campaign_notices();
        $html = ob_get_clean();
        $this->assertStringContainsString( 'notice-error', $html );
        $this->assertStringContainsString( 'Unable to send campaign. Please verify your URL settings and try again.', $html );
    }
    /**
     * Tests settings submit label script injection.
     */
    public function test_settings_submit_label_adds_inline_script_on_settings_screen()
    {
        set_current_screen( 'settings_page_n8n_mt_settings' );
        wp_register_script( 'jquery-core', false, [], false, true );
        $controller = new AdminController;
        $controller->settings_submit_label();
        $script_data = wp_scripts()->get_data( 'jquery-core', 'after' );
        $combined = is_array( $script_data ) ? implode( "\n", $script_data ) : (string) $script_data;
        $this->assertStringContainsString( 'Save Settings', $combined );
    }
}
