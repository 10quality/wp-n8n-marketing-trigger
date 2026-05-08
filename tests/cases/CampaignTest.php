<?php

namespace N8nMarketingTrigger\Tests\Cases;

use N8nMarketingTrigger\Models\Campaign;
use N8nMarketingTrigger\Controllers\CampaignAdminController;
use N8nMarketingTrigger\Controllers\CampaignController;
use ReflectionClass;
use WP_UnitTestCase;

/**
 * Campaign model tests.
 */
class CampaignTest extends WP_UnitTestCase
{
    /**
     * Tests platform options match expected keys.
     */
    public function test_platform_options_contains_all_expected_keys()
    {
        $options = Campaign::platform_options();
        $this->assertArrayHasKey( 'wordpress-blog', $options );
        $this->assertArrayHasKey( 'linkedin', $options );
        $this->assertArrayHasKey( 'facebook', $options );
        $this->assertArrayHasKey( 'instagram', $options );
        $this->assertArrayHasKey( 'x', $options );
    }
    /**
     * Tests campaign trigger view when campaign is not saved.
     */
    public function test_render_trigger_buttons_shows_save_message_without_post_id()
    {
        $campaign = new Campaign;
        ob_start();
        $campaign->render_trigger_buttons( $campaign );
        $html = ob_get_clean();
        $this->assertStringContainsString( 'Save this campaign before sending triggers.', $html );
    }
    /**
     * Tests payload generation structure.
     */
    public function test_controller_build_payload_matches_expected_structure()
    {
        $admin_controller = new CampaignAdminController;
        $admin_controller->register_post_type();
        $post_id = self::factory()->post->create(
            [
                'post_type' => Campaign::TYPE,
                'post_title' => 'Campaign title',
                'post_content' => 'Campaign body',
            ]
        );
        $campaign = Campaign::find( $post_id );
        $campaign->platforms = [ 'wordpress-blog', 'linkedin' ];
        $campaign->with_cover_image = true;
        $campaign->cover_image_instructions = 'Some prompt';
        $campaign->additional_images = false;
        $controller = new CampaignController;
        $reflection = new ReflectionClass( $controller );
        $method = $reflection->getMethod( 'build_payload' );
        $method->setAccessible( true );
        $payload = $method->invoke( $controller, $campaign );
        $this->assertSame( $post_id, $payload['campaign_id'] );
        $this->assertSame( 'Campaign title', $payload['title'] );
        $this->assertSame( 'Campaign body', $payload['instructions'] );
        $this->assertSame( [ 'wordpress-blog', 'linkedin' ], $payload['settings']['platforms'] );
        $this->assertTrue( $payload['settings']['with_cover_image'] );
        $this->assertSame( 'Some prompt', $payload['settings']['cover_image_instructions'] );
        $this->assertFalse( $payload['settings']['additional_images'] );
        $this->assertArrayHasKey( 'send_at', $payload );
    }
    /**
     * Tests campaign model can load when _wpmvc_model is JSON array string.
     */
    public function test_model_load_does_not_crash_with_array_json_model_meta()
    {
        $admin_controller = new CampaignAdminController;
        $admin_controller->register_post_type();
        $post_id = self::factory()->post->create(
            [
                'post_type' => Campaign::TYPE,
                'post_title' => 'Campaign load',
            ]
        );
        update_post_meta( $post_id, '_wpmvc_model', '[]' );
        $campaign = Campaign::find( $post_id );
        $this->assertInstanceOf( Campaign::class, $campaign );
        $this->assertSame( $post_id, (int) $campaign->ID );
    }
}
