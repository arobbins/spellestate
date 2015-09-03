<?php
/**
 * Import & Export page controller class
 */
 
 
// Prevent direct call
if ( !defined( 'WPINC' ) ) die;
if ( !class_exists( 'GW_GoPricing' ) ) die;	


// Class
class GW_GoPricing_AdminPage_Impex extends GW_GoPricing_AdminPage {
	
	/**
	 * Register ajax actions
	 *
	 * @return void
	 */	
	
	public function register_ajax_actions( $ajax_action_callback ) { 
	
		GW_GoPricing_Admin::register_ajax_action( 'impex', $ajax_action_callback );
		GW_GoPricing_Admin::register_ajax_action( 'import', $ajax_action_callback );	
	}
	
	
	/**
	 * Action
	 *
	 * @return void
	 */
	 	
	public function action() {
		
		// Create custom nonce
		$this->create_nonce( 'impex' );

		// Load views if action is empty		
		if ( empty( $this->action ) ) {
			
			$action = isset( $_GET['action'] ) ? $_GET['action'] : '';
			
			switch ( $action ) {
				
				case 'import':
				
					$tmp_postdata = $this->get_temp_postdata();
					
					if ( empty( $tmp_postdata ) ) {
						// Load default view
						$this->content( $this->view() );
					} else {
						// Load import view
						$this->content( $this->view( 'import' ) );
					}
					break;					
				
				case 'export':
				
					$tmp_postdata = $this->get_temp_postdata();
					
					if ( empty( $tmp_postdata ) ) {
						// Load default view
						$this->content( $this->view() );
						
					} else {
						// Load export view
						$this->content( $this->view( 'export' ) );
					
					}
					break;

				default:
				
					// Load default view
					$this->content( $this->view() );	

			}
			
		}
		
		
		// Load views if action is not empty (handle postdata)
		if ( !empty( $this->action ) && check_admin_referer( $this->nonce, '_nonce' ) ) {
			
			switch( $this->action ) {
				
				// Default
				case 'impex': 
					
					if ( !empty( $this->action_type ) ) {
						
						switch( $this->action_type ) {

							// Import
							case 'import':								
								
								$result = $this->validate_import_data( stripslashes( $_POST['import-data'] ) );	
																
								if ( $result === false ) {
									
									if ( $this->is_ajax === false ) {
										wp_redirect( $this->referrer );	
										exit;
									} else {
										GW_GoPricing_AdminNotices::show();
									}
									
								} else {
									
									$this->set_temp_postdata( array( 'result' => $result, 'data' => stripslashes( $_POST['import-data'] ) ) );
									
									if ( $this->is_ajax === false ) {
										wp_redirect( add_query_arg( 'action', 'import', $this->referrer ) );	
										exit;
									} else {
										echo $this->view( 'import' );
									}
									
								}
												
								break;
							
							// Export	
							case 'export':
								
								$result = $this->validate_export_data( $_POST['export'] );
								
								if ( $result === false ) {

									if ( $this->is_ajax === false ) {
										wp_redirect( $this->referrer );	
										exit;
									} else {
										GW_GoPricing_AdminNotices::show();
									}
									
								} else {
									
									$this->set_temp_postdata( $result );
									
									if ( $this->is_ajax === false ) {
										wp_redirect( add_query_arg( 'action', 'export', $this->referrer ) );	
										exit;
									} else {
										echo $this->view( 'export' );
									}

								}
															
								break;								
							
						}
						
					}

					break;

				// Import page
				case 'import':

					$result = $this->validate_import_data( $_POST['import-data'] );	
					
					if ( $result !== false ) {
				
						if ( !empty( $_POST['import'] ) ) {
							
							$this->import( $_POST['import-data'], ( isset( $_POST['replace'] ) ? $_POST['replace'] : false ), $_POST['import'] );
							
							if ( $this->is_ajax === false ) {
								wp_redirect( $this->referrer );	
								exit;
							} else {
								echo $this->view();
								GW_GoPricing_AdminNotices::show();
							}
				
						} else {
							
							GW_GoPricing_AdminNotices::add( 'impex', 'error', __( 'Please select tables to import!', 'go_pricing_textdomain' ) );
						
							if ( $this->is_ajax === false ) {
								$this->set_temp_postdata( $_POST['import-data'] );
								wp_redirect( add_query_arg( 'action', 'import', $this->referrer ) );	
								exit;
							} else {
								GW_GoPricing_AdminNotices::show();
							}						
							
						}
						
					}

			}
			
		}
			
		
	}
	
	
	/**
	 * Load views
	 *
	 * @return void
	 */	
	
	public function view( $view = '' ) {

		ob_start();
		
		switch( $view ) {
			case 'export' :
				include_once( 'views/page/export.php' );	
				break;
				
			case 'import' : 
				include_once( 'views/page/import.php' );	
				break;
			
			default:
				include_once( 'views/page/impex.php' );				
		};
		
		$view_content = ob_get_clean();	
		return $view_content;
		
	}

	
	/**
	 * Validate & export data
	 *
	 * @return string | bool
	 */		

	public function validate_export_data( $export_data ) { 
		
		if ( empty( $export_data ) ) {

			GW_GoPricing_AdminNotices::add( 'impex', 'error', __( 'There is nothing to export!', 'go_pricing_textdomain' ) );
			return false;
			
		} else {
			
			$export_data = $export_data[0] == 'all' ? array() : $export_data;
			$result = GW_GoPricing_Data::export( $export_data );
			
			if ( $result === false ) { 
			
				GW_GoPricing_AdminNotices::add( 'impex', 'error', __( 'Oops, something went wrong!', 'go_pricing_textdomain' ) );	
				return false;

			}
			
		}
		
		if ( empty( $export_data ) ) $export_data  = 'all';
		
		return $export_data;

	}
	
	
	/**
	 * Import
	 *
	 * @return bool
	 */		

	public function import( $data, $override, $ids ) { 
	
		if ( empty( $data ) ) {
			GW_GoPricing_AdminNotices::add( 'main', 'error', __( 'Import data is missing!', 'go_pricing_textdomain' ) );
			return;
		}
		
		$ids = isset( $ids[0] ) && $ids[0] == 'all' ? array() : $ids;
		$data = GW_GoPricing_Helper::clean_input( $data );
		$result = GW_GoPricing_Data::import( $data, (bool)$override, $ids );
		
		if ( $result === false ) { 
			GW_GoPricing_AdminNotices::add( 'main', 'error', __( 'Oops, something went wrong!', 'go_pricing_textdomain' ) );
		} else {
			GW_GoPricing_AdminNotices::add( 'main', 'success', sprintf( __( '%1$s pricing table(s) has been successfully imported.', 'go_pricing_textdomain' ), $result ) );
		}

	}	
	
	
	/**
	 * Validate & return import data
	 *
	 * @return string | bool
	 */		
	
	public function validate_import_data( $import_data ) {
		
		if ( empty( $import_data ) ) {
		
			GW_GoPricing_AdminNotices::add( 'impex', 'error', __( 'There is nothing to import!', 'go_pricing_textdomain' ) );	
			return false;
			
		}
		
		$result = @unserialize( base64_decode( $import_data ) );
	
		if ( $result === false ) { 

			GW_GoPricing_AdminNotices::add( 'impex', 'error', __( 'Invalid import data!', 'go_pricing_textdomain' ) );
			return false;
			
		}

		if ( empty( $result['_info']['db_version'] ) || version_compare( $result['_info']['db_version'], self::$db_version, "<" ) ) {

			GW_GoPricing_AdminNotices::add( 'impex', 'error', __( 'Import data is not compatible with the current version!', 'go_pricing_textdomain' ) );
		//	return false;

		}
		
		unset( $result['_info'] );
		return $result;
		
	}
	
}
 

?>