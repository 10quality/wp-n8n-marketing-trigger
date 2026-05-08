<?php

namespace N8nMarketingTrigger;

use WPMVC\Bridge;

/**
 * Main class.
 *
 * @package n8n-marketing-trigger
 */
class Main extends Bridge
{
    /**
     * Declaration of public WordPress hooks.
     */
    public function init()
    {
        $this->add_filter( 'asset_base_url', 'AssetController@normalize_base_url', 999 );
        $this->add_filter( 'style_loader_src', 'AssetController@normalize_loader_src', 999, 2 );
        $this->add_filter( 'script_loader_src', 'AssetController@normalize_loader_src', 999, 2 );
        $this->add_action( 'init', 'CampaignAdminController@register_post_type' );
    }
    /**
     * Declaration of admin only WordPress hooks.
     */
    public function on_admin()
    {
        $this->add_filter( 'administrator_models', 'AddonConfigController@administrator_models' );
        $this->add_filter( 'metaboxer_models', 'AddonConfigController@metaboxer_models' );
        $this->add_action( 'admin_post_n8n_mt_send_test', 'CampaignController@send_test' );
        $this->add_action( 'admin_post_n8n_mt_send_campaign', 'CampaignController@send_campaign' );
        $this->add_action( 'admin_notices', 'AdminController@campaign_notices' );
        $this->add_filter( 'post_updated_messages', 'CampaignAdminController@saved_message' );
        $this->add_filter( 'write_your_story', 'CampaignAdminController@body_placeholder', 10, 2 );
        //$this->add_action( 'admin_enqueue_scripts', 'AdminController@settings_submit_label' );
    }
}
