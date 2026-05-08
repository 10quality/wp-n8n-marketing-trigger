<?php

/**
 * Global helper functions.
 *
 * @package n8n-marketing-trigger
 */

use N8nMarketingTrigger\Models\Settings;

if ( ! function_exists( 'n8n_mt' ) ) {
    /**
     * Returns main bridge instance.
     *
     * @return \N8nMarketingTrigger\Main|null
     */
    function n8n_mt()
    {
        return get_bridge( 'N8nMarketingTrigger' );
    }
}
if ( ! function_exists( 'n8n_mt_settings' ) ) {
    /**
     * Returns settings singleton instance.
     *
     * @return \N8nMarketingTrigger\Models\Settings
     */
    function n8n_mt_settings()
    {
        return Settings::instance();
    }
}