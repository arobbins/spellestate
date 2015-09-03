<?php

class Swift_Widget_Data {

	/**
	 * initialize
	 */
	public static function init() {
		if( !is_admin() )
			return;
		
		add_action( 'wp_ajax_import_widget_data', array( __CLASS__, 'ajax_import_widget_data' ) );
		
	}

	/**
	 * Import widgets
	 * @param array $import_array
	 */
	public static function parse_import_data( $import_array ) {
		$sidebars_data = $import_array[0];
		$widget_data = $import_array[1];
		
				
		$current_sidebars = get_option( 'sidebars_widgets' );
		$new_widgets = array( );

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :
		
			foreach ( $import_widgets as $import_widget ) :
				//if the sidebar exists 
					
				if ( array_key_exists($import_sidebar,$current_sidebars) || true ) :
				
					$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					
					
					if ( is_array($current_sidebars[$import_sidebar]) && in_array($import_widget, $current_sidebars[$import_sidebar])) { 
    					continue;
					}
					
					$new_widget_name = self::get_new_widget_name( $title, $index );
					$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );
				
					if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
								$new_index++;
						}
					}
					$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
					
					if ( array_key_exists( $title, $new_widgets ) ) {	
						$new_widgets[$title][$new_index] = $widget_data[$title][$index];
						$multiwidget = $new_widgets[$title]['_multiwidget'];
						
						unset( $new_widgets[$title]['_multiwidget'] );
						$new_widgets[$title]['_multiwidget'] = $multiwidget;
						continue;
					} else {	
						$current_widget_data[$new_index] = $widget_data[$title][$index];
						$current_multiwidget = $current_widget_data['_multiwidget'];
						$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
						$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
						unset( $current_widget_data['_multiwidget'] );
						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[$title] = $current_widget_data;
					}

				endif;
			endforeach;
		endforeach;
		

		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content )
				update_option( 'widget_' . $title, $content );

			return true;
		}

		return false;
	}

	
	
	/**
	 * Parse JSON import file and load
	 */
	public static function ajax_import_widget_data($widget_file) {
		
	
		$response = array(
			'what' => 'widget_import_export',
			'action' => 'import_submit'
		);
			
		
		$json = self::get_widget_settings_json($widget_file);
				
		if( is_wp_error($json) )
			wp_die( $json->get_error_message() );

		if( !$json || !( $json_data = json_decode( $json[0], true ) ) )
				return;

		$import_file = $json[1];

		$widgets = array();
		
		
		if ( isset( $json_data[0] ) ) :
			foreach ( self::order_sidebar_widgets( $json_data[0] ) as $sidebar_name => $widget_list ) :
				if ( count( $widget_list ) == 0 ) {
					continue;
				}
				$sidebar_info = self::get_sidebar_info( $sidebar_name );
				if ( $sidebar_info ) : 
						foreach ( $widget_list as $widget ) :
							$widget_options = false;

							$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
							$widget_type_index = trim( substr( $widget, strrpos( $widget, '-' ) + 1 ) );
							foreach ( $json_data[1] as $name => $option ) {
									if ( $name == $widget_type ) {
										$widget_type_options = $option;
										break;
									}
							}
							
							if ( !isset($widget_type_options) || !$widget_type_options )
								continue;
								
															

							$widget_title = isset( $widget_type_options[$widget_type_index]['title'] ) ? $widget_type_options[$widget_type_index]['title'] : '';
							$widget_options = $widget_type_options[$widget_type_index];
							$widgets[$widget_type][$widget_type_index] = 'on';
										
						endforeach; 
																					
				endif; 
			 endforeach;
		endif; 
								
		
		
		if( empty($widgets) || empty($import_file) ){
					
			$response['id'] = new WP_Error('import_widget_data', 'No widget data posted to import');
			$response = new WP_Ajax_Response( $response );
			$response->send();
		}

		$json_data = file_get_contents( $import_file );
		$json_data = json_decode( $json_data, true );
		$sidebar_data = $json_data[0];
		$widget_data = $json_data[1];
		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i++ ) {
				$widget = array( );
				$widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
				if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
					unset( $sidebar_data[$title][$i] );
				}
			}
			$sidebar_data[$title] = array_values( $sidebar_data[$title] );
		}

		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
		
				$widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
		
			}
		}
		
		$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
		
		
		$response['id'] = ( self::parse_import_data( $sidebar_data ) ) ? true : new WP_Error( 'widget_import_submit', 'Unknown Error' );
		
		//$response = new WP_Ajax_Response( $response );
		//$response->send();
	}

	/**
	 * Read uploaded JSON file
	 * @return type
	 */
	public static function get_widget_settings_json($widget_file) {

		$file_contents = file_get_contents( $widget_file );

		return array( $file_contents, $widget_file);

	}

	
	/**
	 *
	 * @param string $widget_name
	 * @param string $widget_index
	 * @return string
	 */
	public static function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}
	
			
	

	/**
	 *
	 * @global type $wp_registered_sidebars
	 * @param type $sidebar_id
	 * @return boolean
	 */
	public static function get_sidebar_info( $sidebar_id ) {
		global $wp_registered_sidebars;

		//since wp_inactive_widget is only used in widgets.php
		if ( $sidebar_id == 'wp_inactive_widgets' )
			return array( 'name' => 'Inactive Widgets', 'id' => 'wp_inactive_widgets' );

		foreach ( $wp_registered_sidebars as $sidebar ) {
			if ( isset( $sidebar['id'] ) && $sidebar['id'] == $sidebar_id )
				return $sidebar;
		}

		return false;
	}

	/**
	 *
	 * @param array $sidebar_widgets
	 * @return type
	 */
	public static function order_sidebar_widgets( $sidebar_widgets ) {
		$inactive_widgets = false;

		//seperate inactive widget sidebar from other sidebars so it can be moved to the end of the array, if it exists
		if ( isset( $sidebar_widgets['wp_inactive_widgets'] ) ) {
			$inactive_widgets = $sidebar_widgets['wp_inactive_widgets'];
			unset( $sidebar_widgets['wp_inactive_widgets'] );
			$sidebar_widgets['wp_inactive_widgets'] = $inactive_widgets;
		}

		return $sidebar_widgets;
	}


}