<?php

namespace N8nMarketingTrigger\Controllers;

use N8nMarketingTrigger\Models\Campaign;

/**
 * Campaign admin controller.
 *
 * @package n8n-marketing-trigger
 */
class CampaignAdminController
{
    /**
     * Registers campaign post type.
     */
    public function register_post_type()
    {
        $model = new Campaign;
        register_post_type(
            Campaign::TYPE,
            [
                'labels' => $model->registry_labels,
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_icon' => 'dashicons-megaphone',
                'supports' => $model->registry_supports,
                'capability_type' => 'post',
            ]
        );
    }
    /**
     * Sets campaign editor placeholder.
     *
     * @param string|null $text
     * @param \WP_Post|null $post
     *
     * @return string
     */
    public function body_placeholder( $text, $post = null )
    {
        $screen = get_current_screen();
        if ( $screen && isset( $screen->post_type ) && $screen->post_type === Campaign::TYPE ) {
            return __( 'Write your campaign instructions here (for AI)...', 'n8n-marketing-trigger' );
        }
        return is_string( $text ) ? $text : '';
    }
    /**
     * Custom save message.
     *
     * @param array $messages
     *
     * @return array
     */
    public function saved_message( $messages )
    {
        $messages[Campaign::TYPE][1] = __( 'Campaign saved successfully', 'n8n-marketing-trigger' );
        $messages[Campaign::TYPE][6] = __( 'Campaign saved successfully', 'n8n-marketing-trigger' );
        return $messages;
    }
}
