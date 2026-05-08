<?php

namespace N8nMarketingTrigger;

use WPMVC\Bridge;

/**
 * Main class.
 * Bridge between WordPress and App.
 * Class contains declaration of hooks and filters.
 *
 * @author 10 Quality Studio <https://10quality.studio>
 * @package n8n-marketing-trigger
 * @version 1.0.0
 */
class Main extends Bridge
{
    /**
     * Declaration of public WordPress hooks.
     */
    public function init()
    {
    }
    /**
     * Declaration of admin only WordPress hooks.
     * For WordPress admin dashboard.
     */
    public function on_admin()
    {
    }
}