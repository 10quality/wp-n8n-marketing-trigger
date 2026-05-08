<?php

namespace N8nMarketingTrigger\Models;

use WPMVC\MVC\Traits\FindTrait;
use WPMVC\Addons\Metaboxer\Abstracts\PostModel as Model;

/**
 * Campaign model.
 *
 * @package n8n-marketing-trigger
 */
class Campaign extends Model
{
    use FindTrait;
    /**
     * Prevent base model JSON auto-decoding for meta values.
     * Metaboxer expects raw string for _wpmvc_model and decodes it itself.
     *
     * @var bool
     */
    protected $decode_json_meta = false;
    /**
     * Post type.
     *
     * @var string
     */
    const TYPE = 'marketing_campaign';
    /**
     * Post type.
     *
     * @var string
     */
    protected $type = self::TYPE;
    /**
     * Labels.
     *
     * @var array
     */
    protected $registry_labels = [];
    /**
     * Supports.
     *
     * @var array
     */
    protected $registry_supports = [ 'title', 'editor' ];
    /**
     * Initializes metaboxes.
     */
    protected function init()
    {
        $this->registry_labels = [
            'name' => __( 'Campaigns', 'n8n-marketing-trigger' ),
            'singular_name' => __( 'Campaign', 'n8n-marketing-trigger' ),
            'add_new' => __( 'Add New', 'n8n-marketing-trigger' ),
            'add_new_item' => __( 'Add New Campaign', 'n8n-marketing-trigger' ),
            'edit_item' => __( 'Edit Campaign', 'n8n-marketing-trigger' ),
            'new_item' => __( 'New Campaign', 'n8n-marketing-trigger' ),
            'view_item' => __( 'View Campaign', 'n8n-marketing-trigger' ),
            'search_items' => __( 'Search Campaigns', 'n8n-marketing-trigger' ),
            'not_found' => __( 'No campaigns found', 'n8n-marketing-trigger' ),
        ];
        $this->metaboxes = [
            'campaign_settings' => [
                'title' => __( 'Campaign Settings', 'n8n-marketing-trigger' ),
                'context' => 'normal',
                'priority' => 'high',
                'tabs' => [
                    self::NO_TAB => [
                        'fields' => [
                            'platforms' => [
                                'type' => 'select2',
                                'title' => __( 'Platforms', 'n8n-marketing-trigger' ),
                                'options' => self::platform_options(),
                                'control' => [
                                    'wide' => true,
                                    'attributes' => [
                                        'multiple' => true,
                                        'data-allow-clear' => 1,
                                        'placeholder' => __( 'Select platforms...', 'n8n-marketing-trigger' ),
                                    ],
                                ],
                            ],
                            'with_cover_image' => [
                                'type' => 'switch',
                                'title' => __( 'With cover image', 'n8n-marketing-trigger' ),
                                'default' => false,
                                'description' => __( 'If enabled, a cover image will be generated for this campaign using AI. The image will be based on the campaign title and body content.', 'n8n-marketing-trigger' ),
                            ],
                            'cover_image_instructions' => [
                                'type' => 'textarea',
                                'title' => __( 'Cover image instructions', 'n8n-marketing-trigger' ),
                                'description' => __( 'Provide specific instructions for the AI to generate the cover image. For example, you can specify the style, colors, or elements you want in the image.', 'n8n-marketing-trigger' ),
                                'show_if' => [
                                    'with_cover_image' => 1,
                                ],
                                'control' => [
                                    'wide' => true,
                                    'attributes' => [
                                        'rows' => 4,
                                    ],
                                ],
                            ],
                            'additional_images' => [
                                'type' => 'switch',
                                'title' => __( 'Additional images', 'n8n-marketing-trigger' ),
                                'default' => false,
                                'description' => __( 'If enabled, additional images will be generated for this campaign using AI. The images will be based on the campaign title and body content.', 'n8n-marketing-trigger' ),
                                'show_if' => [
                                    'with_cover_image' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'trigger_settings' => [
                'title' => __( 'Trigger Settings', 'n8n-marketing-trigger' ),
                'context' => 'side',
                'priority' => 'high',
                'tabs' => [
                    self::NO_TAB => [
                        'fields' => [
                            'trigger_buttons' => [
                                'type' => 'callback',
                                'callback' => [ $this, 'render_trigger_buttons' ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    /**
     * Enqueues required metabox resources explicitly.
     */
    public function enqueue()
    {
        $plugin_file = dirname( __DIR__, 2 ) . '/plugin.php';
        wp_enqueue_style(
            'n8n-mt-select2',
            plugins_url( 'vendor/10quality/wpmvc-addon-resources/assets/css/select2.min.css', $plugin_file ),
            [],
            '4.0.13'
        );
        wp_enqueue_script(
            'n8n-mt-select2',
            plugins_url( 'vendor/10quality/wpmvc-addon-resources/assets/js/select2.min.js', $plugin_file ),
            [ 'jquery' ],
            '4.0.13',
            true
        );
        wp_enqueue_style(
            'n8n-mt-switch',
            plugins_url( 'vendor/10quality/wpmvc-addon-resources/assets/css/switch.css', $plugin_file ),
            [],
            '1.0.4'
        );
        wp_enqueue_script(
            'wpmvc-hideshow',
            plugins_url( 'vendor/10quality/wpmvc-addon-resources/assets/js/jquery.hide-show.js', $plugin_file ),
            [ 'jquery' ],
            '1.0.0',
            true
        );
        wp_enqueue_script(
            'n8n-mt-switch',
            plugins_url( 'vendor/10quality/wpmvc-addon-resources/assets/js/jquery.switch.js', $plugin_file ),
            [ 'jquery', 'wpmvc-hideshow' ],
            '1.0.5',
            true
        );
        if ( ! $this->ID ) {
            return;
        }
        wp_enqueue_script(
            'n8n-mt-campaign-trigger'
        );
        wp_localize_script(
            'n8n-mt-campaign-trigger',
            'n8nMtCampaignTrigger',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'n8n_mt_send_campaign' ),
                'campaignId' => (int) $this->ID,
                'messages' => [
                    'sending' => __( 'Sending...', 'n8n-marketing-trigger' ),
                    'genericError' => __( 'Unable to send campaign. Please verify your URL settings and try again.', 'n8n-marketing-trigger' ),
                ],
                'actions' => [
                    'test' => 'n8n_mt_send_test',
                    'production' => 'n8n_mt_send_campaign',
                ],
            ]
        );
    }
    /**
     * Render trigger buttons.
     *
     * @param \WPMVC\Addons\Metaboxer\Abstracts\PostModel $model
     * @param string                                      $field_id
     */
    public function render_trigger_buttons( $model, $field_id = '' )
    {
        $settings = n8n_mt_settings();
        $has_test = trim( (string) $settings->test_url ) !== '';
        $has_production = trim( (string) $settings->production_url ) !== '';
        n8n_mt()->view(
            'campaign.trigger-buttons',
            [
                'post_id' => $model->ID,
                'has_test' => $has_test,
                'has_production' => $has_production,
            ]
        );
    }
    /**
     * Platform options.
     *
     * @return array
     */
    public static function platform_options()
    {
        return [
            'wordpress-blog' => __( 'WordPress blog', 'n8n-marketing-trigger' ),
            'linkedin' => __( 'LinkedIn', 'n8n-marketing-trigger' ),
            'facebook' => __( 'Facebook', 'n8n-marketing-trigger' ),
            'instagram' => __( 'Instagram', 'n8n-marketing-trigger' ),
            'x' => __( 'X', 'n8n-marketing-trigger' ),
        ];
    }
}
