<?php

/**
 * Trigger uninstall of the plugins
 */
/**
 * @package akashPlugin
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Access the database via SQL
global $wpdb;
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'smp_poll'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
