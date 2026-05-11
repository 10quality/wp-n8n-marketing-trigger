<?php

namespace N8nMarketingTrigger\Controllers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use N8nMarketingTrigger\Models\Campaign;
use WPMVC\Log;
use WPMVC\Request;

/**
 * Campaign trigger controller.
 *
 * @package n8n-marketing-trigger
 */
class CampaignController
{
    /**
     * Scheduled event action name.
     *
     * @var string
     */
    const SCHEDULED_ACTION = 'n8n_mt_run_scheduled_campaign';
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
            $this->send_webhook( $campaign, $target_url );
            $this->respond_success( $mode, $is_ajax, $campaign_id );
        } catch ( RequestException $e ) {
            $this->respond_error( $this->extract_request_error_message( $e ), $is_ajax, $campaign_id );
        } catch ( Exception $e ) {
            $this->respond_error( __( 'Unable to send campaign. Please verify your URL settings and try again.', 'n8n-marketing-trigger' ), $is_ajax, $campaign_id );
        }
    }
    /**
     * Synchronizes campaign scheduled trigger on save.
     *
     * @param int      $post_id
     * @param \WP_Post $post
     * @param bool     $update
     */
    public function sync_scheduled_trigger( $post_id, $post, $update )
    {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $campaign = Campaign::find( (int) $post_id );
        if ( ! $campaign || $campaign->post_type !== Campaign::TYPE ) {
            return;
        }
        wp_clear_scheduled_hook( self::SCHEDULED_ACTION, [ (int) $post_id ] );
        if ( ! $campaign->scheduled_enabled ) {
            return;
        }
        $timezone = trim( (string) $campaign->scheduled_timezone );
        $scheduled_datetime = trim( (string) $campaign->scheduled_datetime );
        $timestamp = $this->parse_scheduled_timestamp( $scheduled_datetime, $timezone );
        if ( ! $timestamp || $timestamp <= time() ) {
            Log::info( sprintf( 'Campaign %d scheduled trigger ignored: invalid or past datetime "%s" (%s).', (int) $post_id, $scheduled_datetime, $timezone ) );
            return;
        }
        wp_schedule_single_event( $timestamp, self::SCHEDULED_ACTION, [ (int) $post_id ] );
        Log::info( sprintf( 'Campaign %d scheduled for %s (%s).', (int) $post_id, gmdate( 'c', $timestamp ), $timezone ) );
    }
    /**
     * Runs scheduled campaign trigger.
     *
     * @param int $campaign_id
     */
    public function run_scheduled_trigger( $campaign_id )
    {
        $campaign_id = (int) $campaign_id;
        if ( $campaign_id <= 0 ) {
            return;
        }
        $campaign = Campaign::find( $campaign_id );
        if ( ! $campaign || $campaign->post_type !== Campaign::TYPE ) {
            Log::info( sprintf( 'Scheduled campaign %d not found.', $campaign_id ) );
            return;
        }
        if ( ! $campaign->scheduled_enabled ) {
            Log::info( sprintf( 'Scheduled campaign %d skipped because schedule is disabled.', $campaign_id ) );
            return;
        }
        $mode = $campaign->scheduled_webhook === 'test' ? 'test' : 'production';
        $settings = n8n_mt_settings();
        $target_url = $mode === 'test' ? trim( (string) $settings->test_url ) : trim( (string) $settings->production_url );
        if ( $target_url === '' ) {
            Log::info( sprintf( 'Scheduled campaign %d skipped: empty webhook URL for mode %s.', $campaign_id, $mode ) );
            return;
        }
        try {
            $this->send_webhook( $campaign, $target_url );
            Log::info( sprintf( 'Scheduled campaign %d sent using %s webhook.', $campaign_id, $mode ) );
        } catch ( Exception $e ) {
            Log::error( $e );
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
     * Sends webhook payload to target URL.
     *
     * @param \N8nMarketingTrigger\Models\Campaign $campaign
     * @param string                               $target_url
     */
    protected function send_webhook( Campaign $campaign, $target_url )
    {
        $client = new Client( [
            'timeout' => 20,
            'http_errors' => true,
        ] );
        $client->request( 'POST', $target_url, [
            'json' => $this->build_payload( $campaign ),
        ] );
    }
    /**
     * Parses local scheduled datetime + timezone into UTC timestamp.
     *
     * @param string $datetime
     * @param string $timezone
     *
     * @return int|null
     */
    protected function parse_scheduled_timestamp( $datetime, $timezone )
    {
        if ( $datetime === '' || $timezone === '' ) {
            return null;
        }
        try {
            $timezone_object = new \DateTimeZone( $timezone );
        } catch ( Exception $e ) {
            return null;
        }
        $formats = [ 'Y/m/d H:i', 'Y-m-d H:i', 'Y-m-d\TH:i', 'm/d/Y H:i', 'd/m/Y H:i' ];
        foreach ( $formats as $format ) {
            $parsed = \DateTimeImmutable::createFromFormat( $format, $datetime, $timezone_object );
            if ( $parsed instanceof \DateTimeImmutable ) {
                return $parsed->getTimestamp();
            }
        }
        try {
            $fallback = new \DateTimeImmutable( $datetime, $timezone_object );
            return $fallback->getTimestamp();
        } catch ( Exception $e ) {
            return null;
        }
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
