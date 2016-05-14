<?php

/**
 * Plugin Name: Add Editor To Page For Posts
 * Description: Reinstate the WP Editor on the "page for posts" page after it was removed in 4.2.2 update.
 * Version: 0.1
 * Author: Christopher Geary
 * Author URI: http://crgeary.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if( ! function_exists('crgeary_fix_no_editor_on_posts_page'))
{
	/**
	 * Add the wp-editor back into WordPress after it was removed in 4.2.2 (ish).
	 * 
	 * @param $post
	 * @return void
	 */
	function crgeary_fix_no_editor_on_posts_page($post)
	{
		if($post->ID != get_option('page_for_posts') || post_type_supports('page', 'editor'))
			return;
		
		remove_action('edit_form_after_title', '_wp_posts_page_notice');
		add_post_type_support('page', 'editor');
	}
	add_action('edit_form_after_title', 'crgeary_fix_no_editor_on_posts_page', 0);
}