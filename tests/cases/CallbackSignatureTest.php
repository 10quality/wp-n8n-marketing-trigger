<?php

namespace N8nMarketingTrigger\Tests\Cases;

use N8nMarketingTrigger\Models\Campaign;
use N8nMarketingTrigger\Models\Settings;
use WP_UnitTestCase;

/**
 * Callback signature compatibility tests.
 */
class CallbackSignatureTest extends WP_UnitTestCase
{
    /**
     * Settings callback should accept addon args.
     */
    public function test_settings_payload_callback_accepts_two_arguments()
    {
        $settings = Settings::instance();
        ob_start();
        $settings->render_payload_example( $settings, 'payload_example' );
        $html = ob_get_clean();
        $this->assertStringContainsString( '<pre ', $html );
    }
    /**
     * Campaign callback should accept addon args.
     */
    public function test_campaign_trigger_callback_accepts_two_arguments()
    {
        $campaign = new Campaign;
        ob_start();
        $campaign->render_trigger_buttons( $campaign, 'trigger_buttons' );
        $html = ob_get_clean();
        $this->assertStringContainsString( 'Save this campaign before sending triggers.', $html );
    }
}
