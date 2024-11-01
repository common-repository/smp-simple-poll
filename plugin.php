<?php
/*
Plugin Name: Simple Poll
Plugin Uri: https://github.com/akashmdiu/smp-simple-poll
Description: The Simple Poll is a voting poll system into your post, pages and everywhere in website by just a shortcode. Add poll system to your post by placing shortcode.
Author: Akash Mia
Author URI: https://bprogrammer.net
Version: 1.1.4
Tags: simple poll, voting poll, survay, poll by shortcode, create poll.
Text Domain: smp-simple-poll
Domain Path: /languages/
Licence: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


/*###############################################################
    Simple Poll 1.0.0 A simple poll system for WordPress
##############################################################*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


/********ACTIVATOR********/
register_activation_hook(__FILE__, 'smp_poll_active');

//Simple Poll Activation
if (!function_exists('smp_poll_active')) {
    function smp_poll_active()
    { }
} else {
    $plugin = dirname(__FILE__) . '/smp-simple-poll.php';
    deactivate_plugins($plugin);

    wp_die('<div class="plugins"><h2>Simple Poll 1.0.0 Plugin Activation Error!</h2><p style="background: #ffef80;padding: 10px 15px;border: 1px solid #ffc680;">We Found that you are using Our Plugin\'s Another Version, Please Deactivate That Version & than try to re-activate it. Don\'t worry free plugins data will be automatically migrate into this version. Thanks!</p></div>', 'Plugin Activation Error', array('response' => 200, 'back_link' => true));
}


/*********DEACTIVATOR*********/
register_deactivation_hook(__FILE__, 'smp_poll_deactive');

/** 
 *Simple Poll Deactivation
 */

if (!function_exists('smp_poll_deactive')) {
    function smp_poll_deactive()
    { }
}

/**
 * Load text domain
 */

if (!function_exists('smp_poll_load_textdomain')) {
    function smp_poll_load_textdomain()
    {
        load_plugin_textdomain('smp-simple-poll', false, dirname(__FILE__) . "/languages");
    }
    add_action('plugins_loaded', 'smp_poll_load_textdomain');
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path(__FILE__) . 'src/init.php';

/**
 *  Poll Initializer.
 */
require_once plugin_dir_path(__FILE__) . 'smp-simple-poll.php';
