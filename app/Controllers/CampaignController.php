<?php

namespace N8nMarketingTrigger\Controllers;

use Exception;
use GuzzleHttp\Client;
use N8nMarketingTrigger\Models\Campaign;
use WPMVC\Request;

/**
 * Campaign trigger controller.
 *
 * @package n8n-marketing-trigger
 */
class CampaignController
{
    /**
     * Sends test trigger.
     */
    public function send_test()
    {
        $this->handle_send( 'test' );
    }
    /**
     * Sends production trigger.
     */
    public function send_campaign()
    {
        $this->handle_send( 'production' );
    }
    /**
     * Handles webhook send.
     *
     * @param string $mode
     */
    protected function handle_send( $mode )
    {
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'n8n-marketing-trigger' ) );
        }
        check_admin_referer( 'n8n_mt_send_campaign', 'n8n_mt_nonce' );
        $campaign_id = Request::input( 'campaign_id', 0 );
        $campaign = Campaign::find( $campaign_id );
        if ( ! $campaign || $campaign->post_type !== Campaign::TYPE ) {
            $this->redirect_with_notice( 0, 'send_failed' );
        }
        $settings = n8n_mt_settings();
        $target_url = $mode === 'test' ? trim( (string) $settings->test_url ) : trim( (string) $settings->production_url );
        if ( empty( $target_url ) ) {
            $this->redirect_with_notice( $campaign_id, 'send_failed' );
        }
        try {
            $client = new Client( [
                'timeout' => 20,
                'http_errors' => true,
            ] );
            $client->request( 'POST', $target_url, [
                'json' => $this->build_payload( $campaign ),
            ] );
            $this->redirect_with_notice( $campaign_id, $mode === 'test' ? 'test_sent' : 'campaign_sent' );
        } catch ( Exception $e ) {
            $this->redirect_with_notice( $campaign_id, 'send_failed' );
        }
    }
    /**
     * Builds payload for campaign webhook.
     *
     * @param \N8nMarketingTrigger\Models\Campaign $campaign
     *
     * @return array
     */
    protected function build_payload( Campaign $campaign )
    {
        $platforms = is_array( $campaign->platforms ) ? $campaign->platforms : [];
        return [
            'send_at' => gmdate( 'c' ),
            'campaign_id' => (int) $campaign->ID,
            'title' => (string) $campaign->post_title,
            'instructions' => (string) $campaign->post_content,
            'settings' => [
                'platforms' => $platforms,
                'with_cover_image' => (bool) $campaign->with_cover_image,
                'cover_image_instructions' => (string) $campaign->cover_image_instructions,
                'additional_images' => (bool) $campaign->additional_images,
            ],
        ];
    }
    /**
     * Redirects with admin notice flag.
     *
     * @param int    $campaign_id
     * @param string $notice
     */
    protected function redirect_with_notice( $campaign_id, $notice )
    {
        $url = $campaign_id > 0
            ? admin_url( 'post.php?post=' . (int) $campaign_id . '&action=edit' )
            : admin_url( 'edit.php?post_type=' . Campaign::TYPE );
        wp_safe_redirect( add_query_arg( 'n8n_mt_notice', $notice, $url ) );
        exit;
    }
}
