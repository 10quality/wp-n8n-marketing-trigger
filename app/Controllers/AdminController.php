<?php

namespace N8nMarketingTrigger\Controllers;

use WPMVC\Request;

/**
 * Admin UI controller.
 *
 * @package n8n-marketing-trigger
 */
class AdminController
{
    /**
     * Campaign send notices.
     */
    public function campaign_notices()
    {
        $notice = Request::input( 'n8n_mt_notice' );
        if ( empty( $notice ) ) {
            return;
        }
        if ( $notice === 'test_sent' ) {
            n8n_mt()->view( 'notices.success', [ 'message' => __( 'Test campaign sent successfully', 'n8n-marketing-trigger' ) ] );
        } elseif ( $notice === 'campaign_sent' ) {
            n8n_mt()->view( 'notices.success', [ 'message' => __( 'Campaign sent successfully', 'n8n-marketing-trigger' ) ] );
        } elseif ( $notice === 'send_failed' ) {
            n8n_mt()->view( 'notices.error', [ 'message' => __( 'Unable to send campaign. Please verify your URL settings and try again.', 'n8n-marketing-trigger' ) ] );
        }
    }
    /**
     * Updates settings submit button text.
     */
    public function settings_submit_label()
    {
        $screen = get_current_screen();
        if ( ! $screen || $screen->id !== 'settings_page_n8n_mt_settings' ) {
            return;
        }
        wp_add_inline_script(
            'jquery-core',
            'jQuery(function($){$("input[type=\'submit\'].button-primary").val("Save Settings");});'
        );
    }
}
