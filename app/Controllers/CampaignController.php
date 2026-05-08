<?php

namespace N8nMarketingTrigger\Controllers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
        $this->handle_send( 'test', true );
    }
    /**
     * Sends production trigger.
     */
    public function send_campaign()
    {
        $this->handle_send( 'production', true );
    }
    /**
     * Handles webhook send.
     *
     * @param string $mode
     */
    protected function handle_send( $mode, $is_ajax = false )
    {
        if ( ! current_user_can( 'edit_posts' ) ) {
            $this->respond_error( __( 'You do not have permission to perform this action.', 'n8n-marketing-trigger' ), $is_ajax );
        }
        check_admin_referer( 'n8n_mt_send_campaign', 'n8n_mt_nonce' );
        $campaign_id = Request::input( 'campaign_id', 0 );
        $campaign = Campaign::find( $campaign_id );
        if ( ! $campaign || $campaign->post_type !== Campaign::TYPE ) {
            $this->respond_error( __( 'Campaign was not found.', 'n8n-marketing-trigger' ), $is_ajax, 0 );
        }
        $settings = n8n_mt_settings();
        $target_url = $mode === 'test' ? trim( (string) $settings->test_url ) : trim( (string) $settings->production_url );
        if ( empty( $target_url ) ) {
            $this->respond_error( __( 'Missing target URL in settings.', 'n8n-marketing-trigger' ), $is_ajax, $campaign_id );
        }
        try {
            $client = new Client( [
                'timeout' => 20,
                'http_errors' => true,
            ] );
            $client->request( 'POST', $target_url, [
                'json' => $this->build_payload( $campaign ),
            ] );
            $this->respond_success( $mode, $is_ajax, $campaign_id );
        } catch ( RequestException $e ) {
            $this->respond_error( $this->extract_request_error_message( $e ), $is_ajax, $campaign_id );
        } catch ( Exception $e ) {
            $this->respond_error( __( 'Unable to send campaign. Please verify your URL settings and try again.', 'n8n-marketing-trigger' ), $is_ajax, $campaign_id );
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
        $settings = n8n_mt_settings();
        return [
            'send_at' => gmdate( 'c' ),
            'url' => home_url(),
            'campaign_id' => (int) $campaign->ID,
            'title' => (string) $campaign->post_title,
            'instructions' => (string) $campaign->post_content,
            'settings' => [
                'platforms' => $platforms,
                'with_cover_image' => (bool) $campaign->with_cover_image,
                'cover_image_instructions' => (string) $campaign->cover_image_instructions,
                'additional_images' => (bool) $campaign->additional_images,
                'goal' => (string) $campaign->goal,
                'target_audience' => (string) $campaign->target_audience,
                'call_to_action' => $this->resolve_call_to_action( $campaign->call_to_action ),
                'alternative_call_to_action' => (string) $campaign->alternative_call_to_action,
            ],
            'business' => [
                'name' => (string) $settings->business_name,
                'phone' => (string) $settings->business_phone,
                'email' => (string) $settings->business_email,
                'description' => (string) $settings->business_description,
            ],
        ];
    }
    /**
     * Resolves selected call-to-action page IDs into payload objects.
     *
     * @param mixed $selected
     *
     * @return array
     */
    protected function resolve_call_to_action( $selected )
    {
        $ids = is_array( $selected ) ? $selected : [];
        $calls_to_action = [];
        foreach ( $ids as $id ) {
            $page_id = (int) $id;
            if ( $page_id <= 0 ) {
                continue;
            }
            $page = get_post( $page_id );
            if ( ! $page || $page->post_type !== 'page' ) {
                continue;
            }
            $calls_to_action[] = [
                'id' => $page_id,
                'title' => (string) $page->post_title,
                'url' => get_permalink( $page_id ),
            ];
        }
        return $calls_to_action;
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
    /**
     * Responds with a success message.
     *
     * @param string $mode
     * @param bool   $is_ajax
     * @param int    $campaign_id
     */
    protected function respond_success( $mode, $is_ajax, $campaign_id )
    {
        $message = $mode === 'test'
            ? __( 'Test campaign sent successfully', 'n8n-marketing-trigger' )
            : __( 'Campaign sent successfully', 'n8n-marketing-trigger' );
        if ( $is_ajax ) {
            wp_send_json_success( [ 'message' => $message ] );
        }
        $this->redirect_with_notice( $campaign_id, $mode === 'test' ? 'test_sent' : 'campaign_sent' );
    }
    /**
     * Responds with an error message.
     *
     * @param string $message
     * @param bool   $is_ajax
     * @param int    $campaign_id
     */
    protected function respond_error( $message, $is_ajax, $campaign_id = 0 )
    {
        if ( $is_ajax ) {
            wp_send_json_error( [ 'message' => $message ] );
        }
        $this->redirect_with_notice( $campaign_id, 'send_failed' );
    }
    /**
     * Builds user-safe message from request exception.
     *
     * @param \GuzzleHttp\Exception\RequestException $e
     *
     * @return string
     */
    protected function extract_request_error_message( RequestException $e )
    {
        $response = $e->getResponse();
        if ( ! $response ) {
            return __( 'Unable to reach webhook endpoint.', 'n8n-marketing-trigger' );
        }
        $status = (int) $response->getStatusCode();
        $body = trim( (string) $response->getBody() );
        if ( $body !== '' ) {
            $decoded = json_decode( $body, true );
            if ( is_array( $decoded ) && ! empty( $decoded['message'] ) ) {
                return sprintf(
                    /* translators: 1: HTTP status code, 2: webhook error message */
                    __( 'Webhook error (%1$d): %2$s', 'n8n-marketing-trigger' ),
                    $status,
                    (string) $decoded['message']
                );
            }
        }
        return sprintf(
            /* translators: %d: HTTP status code */
            __( 'Webhook request failed with status %d.', 'n8n-marketing-trigger' ),
            $status
        );
    }
}
