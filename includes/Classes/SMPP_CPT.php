<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Classes;

class SMPP_CPT
{
    public function register()
    {
        // Hook into the 'init' action
        add_action('init', array($this, 'smpp_cpt'));
    }

    /**
     * Register SMPP custom post type 
     */
    public static function smpp_cpt()
    {

        $labels = array(
            'name'                => _x('Simple Poll', 'smp-simple-poll'),
            'singular_name'       => _x('Simple Poll',  'smp-simple-poll'),
            'menu_name'           => __('Simple Poll', 'smp-simple-poll'),
            'name_admin_bar'      => __('Simple Polls', 'smp-simple-poll'),
            'parent_item_colon'   => __('Parent Poll:', 'smp-simple-poll'),
            'all_items'           => __('All Polls', 'smp-simple-poll'),
            'add_new_item'        => __('Add New Poll', 'smp-simple-poll'),
            'add_new'             => __('Add New', 'smp-simple-poll'),
            'new_item'            => __('New Poll', 'smp-simple-poll'),
            'edit_item'           => __('Edit Poll', 'smp-simple-poll'),
            'update_item'         => __('Update Poll', 'smp-simple-poll'),
            'view_item'           => __('View Poll', 'smp-simple-poll'),
            'search_items'        => __('Search Poll', 'smp-simple-poll'),
            'not_found'           => __('Not found', 'smp-simple-poll'),
            'not_found_in_trash'  => __('Not found in Trash', 'smp-simple-poll'),
            'featured_image'        => __('Poll Background', 'smp-simple-poll'),
            'set_featured_image'    => __('Set Poll Background', 'smp-simple-poll'),
            'remove_featured_image' => __('Remove Poll Background', 'smp-simple-poll'),
            'use_featured_image'    => __('Use as Poll Background', 'smp-simple-poll'),
            'uploaded_to_this_item' => __('Uploaded to this bank', 'smp-simple-poll'),
        );
        $args = array(
            'label'               => __('Simple Poll', 'smp-simple-poll'),
            'description'         => __('Simple Poll Description', 'smp-simple-poll'),
            'labels'              => $labels,
            'supports'            => array('title', 'revisions',  'thumbnail'),
            'show_in_rest'           => true,
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'              => 'dashicons-chart-pie',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'               => array('slug' => 'poll'),
            'capability_type'     => 'page',
        );
        register_post_type('smpp_poll', $args);
        flush_rewrite_rules(true);
    }
}
