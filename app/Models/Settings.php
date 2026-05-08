<?php

namespace N8nMarketingTrigger\Models;

use WPMVC\MVC\Traits\FindTrait;
use WPMVC\Addons\Administrator\Traits\SettingsTrait;
use WPMVC\Addons\Administrator\Abstracts\SettingsModel as Model;

/**
 * Settings model.
 *
 * @package n8n-marketing-trigger
 */
class Settings extends Model
{
    use FindTrait, SettingsTrait;
    /**
     * Option ID.
     *
     * @var string
     */
    const ID = 'n8n_mt_settings';
    /**
     * Property id.
     *
     * @var string
     */
    protected $id = self::ID;
    /**
     * Initializes model settings.
     */
    protected function init()
    {
        $this->title = __( 'Marketing campaigns', 'n8n-marketing-trigger' );
        $this->menu = [
            'parent' => 'options-general.php',
            'title' => __( 'Marketing campaigns', 'n8n-marketing-trigger' ),
            'capability' => 'manage_options',
        ];
        $this->save_message = __( 'Settings saved successfully', 'n8n-marketing-trigger' );
        $this->tabs = [
            'webhooks' => [
                'title' => __( 'Webhooks', 'n8n-marketing-trigger' ),
                'fields' => [
                    'method' => [
                        'type' => 'input',
                        'title' => __( 'Method', 'n8n-marketing-trigger' ),
                        'default' => 'POST',
                        'control' => [
                            'attributes' => [
                                'disabled' => 'disabled',
                            ],
                        ],
                    ],
                    'test_url' => [
                        'type' => 'input',
                        'title' => __( 'Test URL', 'n8n-marketing-trigger' ),
                        'default' => '',
                        'control' => [
                            'type' => 'url',
                            'wide' => true,
                        ],
                    ],
                    'production_url' => [
                        'type' => 'input',
                        'title' => __( 'Production URL', 'n8n-marketing-trigger' ),
                        'default' => '',
                        'control' => [
                            'type' => 'url',
                            'wide' => true,
                        ],
                    ],
                ],
            ],
            'business' => [
                'title' => __( 'Business', 'n8n-marketing-trigger' ),
                'fields' => [
                    'business_name' => [
                        'type' => 'input',
                        'title' => __( 'Business name', 'n8n-marketing-trigger' ),
                        'default' => '',
                        'control' => [
                            'type' => 'text',
                            'wide' => true,
                        ],
                    ],
                    'business_phone' => [
                        'type' => 'input',
                        'title' => __( 'Phone number', 'n8n-marketing-trigger' ),
                        'default' => '',
                        'control' => [
                            'type' => 'text',
                            'wide' => true,
                        ],
                    ],
                    'business_email' => [
                        'type' => 'input',
                        'title' => __( 'Email', 'n8n-marketing-trigger' ),
                        'default' => '',
                        'control' => [
                            'type' => 'email',
                            'wide' => true,
                        ],
                    ],
                    'business_description' => [
                        'type' => 'textarea',
                        'title' => __( 'What we do', 'n8n-marketing-trigger' ),
                        'default' => '',
                        'control' => [
                            'wide' => true,
                            'attributes' => [
                                'rows' => 4,
                            ],
                        ],
                    ],
                ],
            ],
            'payload' => [
                'title' => __( 'Payload', 'n8n-marketing-trigger' ),
                'submit' => false,
                'fields' => [
                    'payload_example' => [
                        'type' => 'callback',
                        'callback' => [ $this, 'render_payload_example' ],
                    ],
                ],
            ],
        ];
        $this->default_tab = 'webhooks';
    }
    /**
     * Render payload example section.
     *
     * @param \WPMVC\Addons\Administrator\Abstracts\SettingsModel $model
     * @param string                                              $field_id
     */
    public function render_payload_example( $model, $field_id = '' )
    {
        $payload = [
            'send_at' => '2024-01-01T12:00:00Z',
            'url' => 'https://example.com',
            'campaign_id' => 123,
            'title' => 'Campaign title',
            'instructions' => 'Campaign body',
            'settings' => [
                'platforms' => [ 'wordpress-blog', 'linkedin' ],
                'with_cover_image' => true,
                'cover_image_instructions' => 'Generate a cover image with a blue background and the campaign title in white text.',
                'additional_images' => false,
                'goal' => 'Campaign goal',
                'target_audience' => 'Campaign target audience',
                'call_to_action' => [
                    [
                        'id' => 456,
                        'title' => 'Call to action page title',
                        'url' => 'https://example.com/call-to-action-page',
                    ],
                ],
                'alternative_call_to_action' => 'Alternative call to action',
            ],
            'business' => [
                'name' => 'Business name',
                'phone' => '123-456-7890',
                'email' => 'example@example.com',
                'description' => 'Brief description of what the business does.',
            ],
        ];
        n8n_mt()->view(
            'settings.payload-example',
            [
                'payload' => $payload,
            ]
        );
    }
}
