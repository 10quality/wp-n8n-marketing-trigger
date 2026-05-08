<?php

namespace N8nMarketingTrigger\Tests\Cases;

use N8nMarketingTrigger\Controllers\CampaignAdminController;
use N8nMarketingTrigger\Models\Campaign;
use WP_UnitTestCase;

/**
 * Campaign admin controller tests.
 */
class CampaignAdminControllerTest extends WP_UnitTestCase
{
    /**
     * Tests campaign post type registration.
     */
    public function test_register_post_type_registers_marketing_campaign()
    {
        $controller = new CampaignAdminController;
        $controller->register_post_type();
        $this->assertTrue( post_type_exists( Campaign::TYPE ) );
    }
    /**
     * Tests placeholder replacement for campaign editor.
     */
    public function test_body_placeholder_returns_custom_text_for_campaign_screen()
    {
        set_current_screen( 'edit-' . Campaign::TYPE );
        $controller = new CampaignAdminController;
        $value = $controller->body_placeholder( 'Default text' );
        $this->assertSame( 'Write your campaign instructions here (for AI)...', $value );
    }
    /**
     * Tests placeholder remains unchanged for other post types.
     */
    public function test_body_placeholder_keeps_default_for_other_screen()
    {
        set_current_screen( 'edit-post' );
        $controller = new CampaignAdminController;
        $value = $controller->body_placeholder( 'Default text' );
        $this->assertSame( 'Default text', $value );
    }
    /**
     * Tests campaign saved messages are overridden.
     */
    public function test_saved_message_overrides_default_messages()
    {
        $messages = [
            Campaign::TYPE => [
                1 => 'Default',
                6 => 'Default',
            ],
        ];
        $controller = new CampaignAdminController;
        $output = $controller->saved_message( $messages );
        $this->assertSame( 'Campaign saved successfully', $output[Campaign::TYPE][1] );
        $this->assertSame( 'Campaign saved successfully', $output[Campaign::TYPE][6] );
    }
}
