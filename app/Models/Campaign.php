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
                            'goal' => [
                                'type' => 'input',
                                'title' => __( 'Campaign goal', 'n8n-marketing-trigger' ),
                                'control' => [
                                    'type' => 'text',
                                    'wide' => true,
                                ],
                            ],
                            'target_audience' => [
                                'type' => 'input',
                                'title' => __( 'Target audience', 'n8n-marketing-trigger' ),
                                'control' => [
                                    'type' => 'text',
                                    'wide' => true,
                                ],
                            ],
                            'call_to_action' => [
                                'type' => 'select2',
                                'title' => __( 'Call to action', 'n8n-marketing-trigger' ),
                                'options' => self::call_to_action_options(),
                                'control' => [
                                    'wide' => true,
                                    'attributes' => [
                                        'multiple' => true,
                                        'data-allow-clear' => 1,
                                        'placeholder' => __( 'Select pages...', 'n8n-marketing-trigger' ),
                                    ],
                                ],
                            ],
                            'alternative_call_to_action' => [
                                'type' => 'input',
                                'title' => __( 'Alternative call to action', 'n8n-marketing-trigger' ),
                                'control' => [
                                    'type' => 'text',
                                    'wide' => true,
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
                    'manual' => [
                        'title' => __( 'Manual', 'n8n-marketing-trigger' ),
                        'fields' => [
                            'trigger_buttons' => [
                                'type' => 'callback',
                                'callback' => [ $this, 'render_trigger_buttons' ],
                            ],
                        ],
                    ],
                    'scheduled' => [
                        'title' => __( 'Scheduled', 'n8n-marketing-trigger' ),
                        'fields' => [
                            'scheduled_enabled' => [
                                'type' => 'switch',
                                'title' => __( 'Enabled', 'n8n-marketing-trigger' ),
                                'default' => false,
                            ],
                            'scheduled_datetime' => [
                                'type' => 'datetimepicker',
                                'title' => __( 'Date and Time', 'n8n-marketing-trigger' ),
                                'show_if' => [
                                    'scheduled_enabled' => 1,
                                ],
                                'control' => [
                                    'wide' => true,
                                    'attributes' => [
                                        'data-n8n-mt-min-now' => 1,
                                    ],
                                ],
                            ],
                            'scheduled_timezone' => [
                                'type' => 'select',
                                'title' => __( 'Timezone', 'n8n-marketing-trigger' ),
                                'options' => self::timezone_options(),
                                'show_if' => [
                                    'scheduled_enabled' => 1,
                                ],
                                'control' => [
                                    'wide' => true,
                                ],
                            ],
                            'scheduled_webhook' => [
                                'type' => 'choose',
                                'title' => __( 'Webhook', 'n8n-marketing-trigger' ),
                                'default' => 'production',
                                'options' => [
                                    'production' => __( 'Prod', 'n8n-marketing-trigger' ),
                                    'test' => __( 'Test', 'n8n-marketing-trigger' ),
                                ],
                                'show_if' => [
                                    'scheduled_enabled' => 1,
                                ],
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
        wpmvc_enqueue_addon_resource( 'wpmvc-select2' );
        wpmvc_enqueue_addon_resource( 'wpmvc-hideshow' );
        wpmvc_enqueue_addon_resource( 'wpmvc-switch' );
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
                'schedule' => [
                    'minDate' => 0,
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
    /**
     * Call-to-action options based on WordPress pages.
     *
     * @return array
     */
    public static function call_to_action_options()
    {
        $options = [];
        $pages = get_pages(
            [
                'post_status' => 'publish',
                'sort_column' => 'post_title',
                'sort_order' => 'ASC',
            ]
        );
        foreach ( $pages as $page ) {
            $options[(string) $page->ID] = $page->post_title;
        }
        return $options;
    }
    /**
     * Timezone select options.
     *
     * @return array
     */
    public static function timezone_options()
    {
        $options = [];
        $now = time();
        foreach ( timezone_identifiers_list() as $timezone ) {
            $timezone_object = new \DateTimeZone( $timezone );
            $offset_seconds = $timezone_object->getOffset( new \DateTimeImmutable( '@' . $now ) );
            $offset_hours = (int) floor( abs( $offset_seconds ) / 3600 );
            $offset_minutes = (int) floor( ( abs( $offset_seconds ) % 3600 ) / 60 );
            $sign = $offset_seconds >= 0 ? '+' : '-';
            $label = sprintf(
                'GMT%s%02d:%02d - %s',
                $sign,
                $offset_hours,
                $offset_minutes,
                $timezone
            );
            $options[$timezone] = $label;
        }
        return $options;
    }
}
