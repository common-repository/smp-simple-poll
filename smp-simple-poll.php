<?php

/**
 * @package SmppSimplePoll
 */


/*
Plugin Name: Simple Poll
Plugin Uri: https://github.com/akashmdiu/smp-simple-poll
Description: The Simple Poll is a voting poll system into your post, pages and everywhere in website by just a shortcode. Add poll system to your post by placing shortcode.
Author: Akash Mia
Author URI: https://bprogrammer.net
Version: 2.0.3
Tags: simple poll, voting poll, survay, poll by shortcode, create poll.
Text Domain: smp-simple-poll
Domain Path: /languages/
Licence: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */



if (!defined('ABSPATH')) {
    die;
}


if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}


use Includes\Base\Activate;
use Includes\Base\Deactivate;


/**
 * Define constant valriable
 */
define('SMPP_PLUGIN_VERSION', '2.0.0');
define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));
define('DIRNAME', dirname(__FILE__));


/**
 *  include smp-simple-poll file here
 */
// require_once PLUGIN_PATH . 'class-simple-poll.php';

if (class_exists('Includes\\Init')) {
    Includes\Init::register_services();
}



if (!class_exists('SmppSimplePoll')) {
    class SmppSimplePoll
    {

        function activate()
        {
            //Generated a CPT
            // $this->smpp_simple_poll_cpt();

            Activate::smpp_activate();

            //flush rewrite rules
            flush_rewrite_rules();
        }

        function deactivate()
        {
            Deactivate::smpp_deactivate();
            flush_rewrite_rules();
        }
    }


    $SmppSimplePoll = new SmppSimplePoll();

    //activation
    register_activation_hook(__FILE__, array($SmppSimplePoll, 'activate'));

    //deactivation
    register_deactivation_hook(__FILE__, array($SmppSimplePoll, 'deactivate'));
}


/**
 * Block Initializer.
 */
require_once plugin_dir_path(__FILE__) . 'src/block_scripts.php';
