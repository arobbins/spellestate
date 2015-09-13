<?php

/*
  Plugin Name: Spell Custom Post Types
  Version: 1.0
  Author: Andrew Robbins - https://simpleblend.net
  Description: Custom Post Types for Spell (Growers, Reviews, Press, etc)
*/

/*
  CPT: Growers
*/
function custom_post_type_growers() {

  $labels = array(
    'name'                => _x('Growers', 'Post Type General Name', 'text_domain'),
    'singular_name'       => _x('Grower', 'Post Type Singular Name', 'text_domain'),
    'menu_name'           => __('Growers', 'text_domain'),
    'parent_item_colon'   => __('Parent Item:', 'text_domain'),
    'new_item'            => __('Add New Grower', 'text_domain'),
    'edit_item'           => __('Edit Grower', 'text_domain'),
    'not_found'           => __('No Grower found', 'text_domain'),
    'not_found_in_trash'  => __('No Grower found in trash', 'text_domain')
  );

  $args = array(
    'label'               => __('Growers', 'text_domain'),
    'description'         => __('Custom Post Type for growers', 'text_domain'),
    'labels'              => $labels,
    'supports'            => array('title'),
    'taxonomies'          => array(),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 100,
    'menu_icon'           => 'dashicons-location',
    'show_in_admin_bar'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'rewrite'             => array( 'slug' => '/growers')
  );

  register_post_type('growers', $args);

}

/*
  CPT: Press
*/
function custom_post_type_press() {

  $labels = array(
    'name'                => _x('Press', 'Post Type General Name', 'text_domain'),
    'singular_name'       => _x('Press', 'Post Type Singular Name', 'text_domain'),
    'menu_name'           => __('Press', 'text_domain'),
    'parent_item_colon'   => __('Parent Item:', 'text_domain'),
    'new_item'            => __('Add New Press', 'text_domain'),
    'edit_item'           => __('Edit Press', 'text_domain'),
    'not_found'           => __('No Press found', 'text_domain'),
    'not_found_in_trash'  => __('No Press found in trash', 'text_domain')
  );

  $args = array(
    'label'               => __('Press', 'text_domain'),
    'description'         => __('Custom Post Type for press', 'text_domain'),
    'labels'              => $labels,
    'supports'            => array('title'),
    'taxonomies'          => array(),
    'hierarchical'        => false,
    'public'              => false,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 100,
    'menu_icon'           => 'dashicons-location',
    'show_in_admin_bar'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'rewrite'             => array( 'slug' => '/press')
  );

  register_post_type('press', $args);

}

/*
  CPT: Reviews
*/
function custom_post_type_reviews() {

  $labels = array(
    'name'                => _x('Reviews', 'Post Type General Name', 'text_domain'),
    'singular_name'       => _x('Reviews', 'Post Type Singular Name', 'text_domain'),
    'menu_name'           => __('Reviews', 'text_domain'),
    'parent_item_colon'   => __('Parent Item:', 'text_domain'),
    'new_item'            => __('Add New Review', 'text_domain'),
    'edit_item'           => __('Edit Review', 'text_domain'),
    'not_found'           => __('No Reviews found', 'text_domain'),
    'not_found_in_trash'  => __('No Reviews found in trash', 'text_domain')
  );

  $args = array(
    'label'               => __('Reviews', 'text_domain'),
    'description'         => __('Custom Post Type for Reviews', 'text_domain'),
    'labels'              => $labels,
    'supports'            => array('title'),
    'taxonomies'          => array(),
    'hierarchical'        => false,
    'public'              => false,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 100,
    'menu_icon'           => 'dashicons-location',
    'show_in_admin_bar'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'rewrite'             => array( 'slug' => '/reviews')
  );

  register_post_type('reviews', $args);

}

// Hookin, yo
add_action('init', 'custom_post_type_growers', 0);
add_action('init', 'custom_post_type_press', 0);
add_action('init', 'custom_post_type_reviews', 0);

?>