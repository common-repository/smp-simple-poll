<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Base;

class TextDomain
{
    public function register()
    {
        add_action('plugins_loaded', [$this, 'smpp_load_textdomain']);
    }

    public function smpp_load_textdomain()
    {
        load_plugin_textdomain('smp-simple-poll', false, dirname(__FILE__) . "/languages");
    }
}
