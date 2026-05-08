<?php

namespace N8nMarketingTrigger\Controllers;

use N8nMarketingTrigger\Models\Campaign;
use N8nMarketingTrigger\Models\Settings;

/**
 * Addon models registration controller.
 *
 * @package n8n-marketing-trigger
 */
class AddonConfigController
{
    /**
     * Registers administrator settings models.
     *
     * @param array $models
     *
     * @return array
     */
    public function administrator_models( $models )
    {
        $models[Settings::ID] = Settings::class;
        return $models;
    }
    /**
     * Registers metaboxer models.
     *
     * @param array $models
     *
     * @return array
     */
    public function metaboxer_models( $models )
    {
        $models['marketing_campaign'] = Campaign::class;
        return $models;
    }
}