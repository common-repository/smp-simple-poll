<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Base;

class Enqueue
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'smpp_backend_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'smpp_frontend_scripts'));
    }

    /**
     *Enqueue all our backend scripts here
     */

    function smpp_backend_scripts()
    {
        wp_register_style('smpp-backend', PLUGIN_URL . '/assets/css/smpp-poll-backend.css');
        wp_enqueue_style(array('smpp-backend'));

        wp_register_script('smpp-backend', PLUGIN_URL . '/assets/js/smpp-poll-backend.js');
        wp_enqueue_script(array('smpp-backend'));
    }

    /**
     *Enqueue all our frontend scripts here
     */

    function smpp_frontend_scripts()
    {
        //Add frontend style
        wp_enqueue_style('smpp-frontend',  PLUGIN_URL . 'assets/css/smpp-poll-frontend.css',  false, rand(23344, 43435));

        //Add frontend sripts
        wp_enqueue_script('smpp-poll-ajax',  PLUGIN_URL . 'assets/js/smpp-ajax-poll.js',  array('jquery'), rand(23344, 43435));
        wp_localize_script('smpp-poll-ajax', 'smpp_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));

        wp_enqueue_script('smpp-frontend',  PLUGIN_URL . 'assets/js/smpp-poll-frontend.js',  false, rand(23344, 43435));
    }
}
