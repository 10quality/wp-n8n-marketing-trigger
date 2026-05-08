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
     * Tests call-to-action options are loaded from pages.
     */
    public function test_call_to_action_options_uses_site_pages()
    {
        $page_id = self::factory()->post->create(
            [
                'post_type' => 'page',
                'post_title' => 'Pricing',
                'post_status' => 'publish',
            ]
        );
        $options = Campaign::call_to_action_options();
        $this->assertArrayHasKey( (string) $page_id, $options );
        $this->assertSame( 'Pricing', $options[(string) $page_id] );
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
        $campaign->goal = 'Increase lead signups';
        $campaign->target_audience = 'Small business owners';
        $cta_page_id = self::factory()->post->create(
            [
                'post_type' => 'page',
                'post_title' => 'Start Here',
                'post_status' => 'publish',
            ]
        );
        $campaign->call_to_action = [ $cta_page_id ];
        $campaign->alternative_call_to_action = 'Call us now';
        $settings = n8n_mt_settings();
        $settings->business_name = 'Acme Inc.';
        $settings->business_phone = '555-0100';
        $settings->business_email = 'hello@example.com';
        $settings->business_description = 'We help teams grow.';
        $controller = new CampaignController;
        $reflection = new ReflectionClass( $controller );
        $method = $reflection->getMethod( 'build_payload' );
        $method->setAccessible( true );
        $payload = $method->invoke( $controller, $campaign );
        $this->assertSame( $post_id, $payload['campaign_id'] );
        $this->assertSame( 'Campaign title', $payload['title'] );
        $this->assertSame( 'Campaign body', $payload['instructions'] );
        $this->assertSame( home_url(), $payload['url'] );
        $this->assertSame( [ 'wordpress-blog', 'linkedin' ], $payload['settings']['platforms'] );
        $this->assertTrue( $payload['settings']['with_cover_image'] );
        $this->assertSame( 'Some prompt', $payload['settings']['cover_image_instructions'] );
        $this->assertFalse( $payload['settings']['additional_images'] );
        $this->assertSame( 'Increase lead signups', $payload['settings']['goal'] );
        $this->assertSame( 'Small business owners', $payload['settings']['target_audience'] );
        $this->assertSame( 'Call us now', $payload['settings']['alternative_call_to_action'] );
        $this->assertCount( 1, $payload['settings']['call_to_action'] );
        $this->assertSame( $cta_page_id, $payload['settings']['call_to_action'][0]['id'] );
        $this->assertSame( 'Start Here', $payload['settings']['call_to_action'][0]['title'] );
        $this->assertSame( get_permalink( $cta_page_id ), $payload['settings']['call_to_action'][0]['url'] );
        $this->assertSame( 'Acme Inc.', $payload['business']['name'] );
        $this->assertSame( '555-0100', $payload['business']['phone'] );
        $this->assertSame( 'hello@example.com', $payload['business']['email'] );
        $this->assertSame( 'We help teams grow.', $payload['business']['description'] );
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
