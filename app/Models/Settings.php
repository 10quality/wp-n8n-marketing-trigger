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
            'campaign_id' => 123,
            'title' => 'Campaign title',
            'instructions' => 'Campaign body',
            'settings' => [
                'platforms' => [ 'wordpress-blog', 'linkedin' ],
                'with_cover_image' => true,
                'cover_image_instructions' => 'Generate a cover image with a blue background and the campaign title in white text.',
                'additional_images' => false,
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
