<?php
/*
Plugin Name: Atelier Demo Content
Description: Replicate any of the Atelier example sites in just a few clicks!
Author: Swift Ideas
Author URI: http://www.swiftideas.net
Version: 2.1
Text Domain: Atelier-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package WordPress
 * @subpackage Importer
 */

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_Swift_Importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_Swift_Importer ) )
		require $class_Swift_Importer;
		
		
}

class Swift_Import extends WP_Importer {
	var $max_wxr_version = 1.2; // max. supported WXR version

	var $id; // WXR attachment ID

	// information to import from WXR file
	var $version;
	var $demofiles = array();
	
	function Swift_Import() { /* nothing */  }
	
	function initialize_data() {
		
		$plugin_path = dirname(__FILE__);
		
		// Demos
		$demofiles['id-0']           = 'demosite';
		$demofiles['title-0']        = 'Atelier Demo Site';
		$demofiles['previewlink-0']  = 'http://Atelier.swiftideas.com/';
		$demofiles['colors-0']       = $plugin_path .'/demofiles/demo0/demosite-colors.json';
		$demofiles['themeoptions-0'] = $plugin_path .'/demofiles/demo0/demosite-options.json';
		$demofiles['widgets-0']      = $plugin_path .'/demofiles/demo0/demosite-widgets.json';
		
		$demofiles['id-1']           = 'form';
		$demofiles['title-1']        = 'Form';
		$demofiles['previewlink-1']  = 'http://atelier.swiftideas.com/form-demo/';
		$demofiles['colors-1']       = $plugin_path .'/demofiles/demo1/form-demo-colors.json';
		$demofiles['themeoptions-1'] = $plugin_path .'/demofiles/demo1/form-demo-options.json';
		$demofiles['widgets-1']      = $plugin_path .'/demofiles/demo1/form-demo-widgets.json';

		$demofiles['id-2']           = 'union';
		$demofiles['title-2']        = 'Union';
		$demofiles['previewlink-2']  = 'http://atelier.swiftideas.com/union-demo/';
		$demofiles['colors-2']       = $plugin_path .'/demofiles/demo2/union-demo-colors.json';
		$demofiles['themeoptions-2'] = $plugin_path .'/demofiles/demo2/union-demo-options.json';
		$demofiles['widgets-2']      = $plugin_path .'/demofiles/demo2/union-demo-widgets.json';
		
		$demofiles['id-3']           = 'convoy';
		$demofiles['title-3']        = 'Convoy';
		$demofiles['previewlink-3']  = 'http://atelier.swiftideas.com/convoy-demo/';
		$demofiles['colors-3']       = $plugin_path .'/demofiles/demo3/convoy-demo-colors.json';
		$demofiles['themeoptions-3'] = $plugin_path .'/demofiles/demo3/convoy-demo-options.json';
		$demofiles['widgets-3']      = $plugin_path .'/demofiles/demo3/convoy-demo-widgets.json';

		$demofiles['id-4']           = 'tilt';
		$demofiles['title-4']        = 'Tilt';
		$demofiles['previewlink-4']  = 'http://atelier.swiftideas.com/tilt-demo/';
		$demofiles['colors-4']       = $plugin_path .'/demofiles/demo4/tilt-demo-colors.json';
		$demofiles['themeoptions-4'] = $plugin_path .'/demofiles/demo4/tilt-demo-options.json';
		$demofiles['widgets-4']      = $plugin_path .'/demofiles/demo4/tilt-demo-widgets.json';

	    $demofiles['id-5']           = 'lab_wines';
		$demofiles['title-5']        = 'Lab';
		$demofiles['previewlink-5']  = 'http://atelier.swiftideas.com/lab-demo/';
		$demofiles['colors-5']       = $plugin_path .'/demofiles/demo5/lab-demo-colors.json';
		$demofiles['themeoptions-5'] = $plugin_path .'/demofiles/demo5/lab-demo-options.json';
		$demofiles['widgets-5']      = $plugin_path .'/demofiles/demo5/lab-demo-widgets.json'; 
		
		$demofiles['id-6']           = 'selby';
		$demofiles['title-6']        = 'Selby';
		$demofiles['previewlink-6']  = 'http://atelier.swiftideas.com/selby-demo/';
		$demofiles['colors-6']       = $plugin_path .'/demofiles/demo6/selby-demo-colors.json';
		$demofiles['themeoptions-6'] = $plugin_path .'/demofiles/demo6/selby-demo-options.json';
		$demofiles['widgets-6']      = $plugin_path .'/demofiles/demo6/selby-demo-widgets.json'; 
		
		$demofiles['id-7']           = 'emigre';
		$demofiles['title-7']        = 'Emigre';
		$demofiles['previewlink-7']  = 'http://atelier.swiftideas.com/emigre-demo/';
		$demofiles['colors-7']       = $plugin_path .'/demofiles/demo7/emigre-demo-colors.json';
		$demofiles['themeoptions-7'] = $plugin_path .'/demofiles/demo7/emigre-demo-options.json';
		$demofiles['widgets-7']      = $plugin_path .'/demofiles/demo7/emigre-demo-widgets.json'; 
			
		$demofiles['id-8']           = 'bryant';
		$demofiles['title-8']        = 'Bryant';
		$demofiles['previewlink-8']  = 'http://atelier.swiftideas.com/bryant-demo';
		$demofiles['colors-8']       = $plugin_path .'/demofiles/demo8/bryant-demo-colors.json';
		$demofiles['themeoptions-8'] = $plugin_path .'/demofiles/demo8/bryant-demo-options.json';
		$demofiles['widgets-8']      = $plugin_path .'/demofiles/demo8/bryant-demo-widgets.json'; 
		
		$demofiles['id-9']           = 'arad';
		$demofiles['title-9']        = 'Arad';
		$demofiles['previewlink-9']  = 'http://atelier.swiftideas.com/arad-demo';
		$demofiles['colors-9']       = $plugin_path .'/demofiles/demo9/arad-demo-colors.json';
		$demofiles['themeoptions-9'] = $plugin_path .'/demofiles/demo9/arad-demo-options.json';
		$demofiles['widgets-9']      = $plugin_path .'/demofiles/demo9/arad-demo-widgets.json'; 
		
		$demofiles['id-10']           = 'flock';
		$demofiles['title-10']        = 'Flock';
		$demofiles['previewlink-10']  = 'http://atelier.swiftideas.com/flock-demo/';
		$demofiles['colors-10']       = $plugin_path .'/demofiles/demo10/flock-demo-colors.json';
		$demofiles['themeoptions-10'] = $plugin_path .'/demofiles/demo10/flock-demo-options.json';
		$demofiles['widgets-10']      = $plugin_path .'/demofiles/demo10/flock-demo-widgets.json'; 
		
		$demofiles['id-11']           = 'porter';
		$demofiles['title-11']        = 'Porter';  
		$demofiles['previewlink-11']  = 'http://atelier.swiftideas.com/porter-demo';
		$demofiles['colors-11']       = $plugin_path .'/demofiles/demo11/porter-demo-colors.json';
		$demofiles['themeoptions-11'] = $plugin_path .'/demofiles/demo11/porter-demo-options.json';
		$demofiles['widgets-11']      = $plugin_path .'/demofiles/demo11/porter-demo-widgets.json'; 
		
		$demofiles['id-12']           = 'vario';
		$demofiles['title-12']        = 'Vario';  
		$demofiles['previewlink-12']  = 'http://atelier.swiftideas.com/vario-demo';
		$demofiles['colors-12']       = $plugin_path .'/demofiles/demo12/vario-demo-colors.json';
		$demofiles['themeoptions-12'] = $plugin_path .'/demofiles/demo12/vario-demo-options.json';
		$demofiles['widgets-12']      = $plugin_path .'/demofiles/demo12/vario-demo-widgets.json'; 
				
		return $demofiles;
	}
	
	/**
	 * Registered callback function for the WordPress Importer
	 *
	 * Manages the three separate stages of the WXR import process
	 */
	function dispatch() {
		
		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) {
			case 0:
			    $this->header();
				$this->greet();
				$this->footer();
				break;
			
		}

		
	}
	
	/**
	 * Get Menu Item Id with special meta data from Custom links
	 * 
	 */
	
	function get_menu_item_special_data_custom($item_name){
		
		  global $wpdb;
		  		 
    	  $query = 'SELECT ID FROM '.$wpdb->posts.', '.$wpdb->term_relationships.', '.$wpdb->postmeta.' ';
     	  $query .= 'WHERE ID = object_id AND ID = post_id ';
          $query .= 'AND post_title = "'.$item_name.'" ';
          $query .= 'AND post_status = "publish" ';
          $query .= 'AND post_type = "nav_menu_item"';
          $query .= 'AND meta_key = "_menu_item_object" order by ID ASC';
          
		  return $wpdb->get_var( $query );
			
	}
	
	/**
	 * Get Menu Item Id with special meta data from Pages
	 * 
	 */
	
	function get_menu_item_special_data_page($item_name){
		
		  global $wpdb;  
		  	  		 
		  if($item_name == 'Home'){
		  		
          		$query = 'SELECT ID FROM '.$wpdb->posts.', '.$wpdb->postmeta.' ';
     	 		$query .= 'WHERE ID = meta_value ';
          		$query .= 'AND post_title = "'.$item_name.'" ';  
          		$query .= 'AND post_status = "publish" ';
          		$query .= 'AND post_type = "page" and menu_order = 1 ';
          		$query .= 'AND meta_key = "_menu_item_object_id" order by post_id asc';
          		
          		
          		
		  }else{
    	  		$query = 'SELECT post_id FROM '.$wpdb->posts.', '.$wpdb->postmeta.' ';
     	 		$query .= 'WHERE ID = meta_value ';
          		$query .= 'AND post_title = "'.$item_name.'" ';
          		$query .= 'AND post_status = "publish" ';
          		$query .= 'AND post_type = "page"';
          		$query .= 'AND meta_key = "_menu_item_object_id" order by post_id asc';
          }
          
		  return $wpdb->get_var( $query );
		  	
	}

	/**
	 * Assign the menus to the locations
	 * 
	 */ 
	
	function assign_menus_to_locations($demoid){
		
		$term = get_term_by('name', 'Main Menu', 'nav_menu');
		
		if($term == null || 0 == $term->term_id){
			$term = get_term_by('name', 'Main', 'nav_menu');	
		}
		
		$menu_id =  $term->term_id;
				
	 	$new_theme_navs = get_theme_mod( 'nav_menu_locations' );
	 	$new_theme_locations = get_registered_nav_menus();
				   
	 	foreach ($new_theme_locations as $location => $description ) {
			
				$new_theme_navs[$location] = $menu_id;	
			
       	}

       	set_theme_mod( 'nav_menu_locations', $new_theme_navs );
					
	}
	
	function set_theme_options( $value = '' ) { 	
		 	 	
            $value['REDUX_last_saved'] = time();
            if( !empty($value) && isset($args) ) {
                $options = $value;
                if ( $args['database'] === 'transient' ) {
                    set_transient( 'sf_atelier_options-transient', $value, time() );
                } else if ( $args['database'] === 'theme_mods' ) {
                    set_theme_mod( $args['opt_name'] . '-mods', $value );
                } else if ( $args['database'] === 'theme_mods_expanded' ) {
                    foreach ( $value as $k=>$v ) {
                        set_theme_mod( $k, $v );
                    }
                } else {
					                    
					   update_option( 'sf_atelier_options', $value  );
                }

                $options = $value;

                /**
                 * action 'redux-saved-{opt_name}'
                 * @deprecated
                 * @param mixed $value set/saved option value
                 */
				 
				 // To work for all the themes this must be replaced redux-saved-{opt_name} and assign a specific value for opt_name 
		
                do_action( "redux-saved-sf_atelier_options", $value ); // REMOVE
				
                /**
                 * action 'redux/options/{opt_name}/saved'
                 * @param mixed $value set/saved option value
                 */
		
				 // To work for all the themes this must be replaced redux-saved-{opt_name} and assign a specific value for opt_name 
                do_action( "redux/options/sf_atelier_options/saved", $value );

            }
        } 
	
	// Display import page title
	function header() {
		echo '<div class="wrap">';
		screen_icon();
		//Welcome message
		echo '<h2 style="margin-left:20px;">' . __( 'Atelier Demo Content Importer', 'wordpress-importer' ) . '</h2>';

		$updates = get_plugin_updates();
		$basename = plugin_basename(__FILE__);
		if ( isset( $updates[$basename] ) ) {
			$update = $updates[$basename];
			echo '<div class="error"><p><strong>';
			printf( __( 'A new version of this importer is available. Please update to version %s to ensure compatibility with newer export files.', 'wordpress-importer' ), $update->update->new_version );
			echo '</strong></p></div>';
		}
	}

	// Close div.wrap
	function footer() {
		echo '</div>';
	}
	



	/**
	 * Display introductory text and file upload form
	 */
	function greet() {
				
		global $plugin_path;

		$demofiles_data = $this->initialize_data();
		$plugin_path = dirname(__FILE__);
		$demourl = admin_url('admin.php?import=swiftdemo&amp;step=1');
		
		?>

		<div class="note-wrap clearfix">
			<h3>Please Read!</h3>
			<p>This demo content importer has been built to make the import process as easy for you as possible. We've done what we can to ensure as little difficulty as possible. We have also gone the extra mile to add in the extra things are sorted for you, such as setting the home page, menu, and widgets - things that aren't possible with the standard WordPress Importer!</p>
			<h4>Steps to take before using this plugin.</h4>
			<ol>
				<li>The import process will work best on a clean install. You can use a plugin such as WordPress Reset to clear your data for you.</li>
				<li>Ensure all plugins are installed beforehand, e.g. WooCommerce - any plugins that you add content to.</li>
				<li>Once you start the process, please leave it running and uninteruppted - the page will refresh and show you a completed message once the process is done.</li>
			</ol>
			<br/>
			<p style="font-weight: bold;">PLEASE NOTE: Due to the large amount of content on the main demo, we have provided a reduced version for you to import to ensure that you have no issues with it.</p>
		</div>
	
	<div>
	
	<?php  	
		
		$html_output = "";		
		if (function_exists('sf_atelier_setup')) {
			$html_output = '<div><ul class="swift-demo">';
			
			for ( $i=0; $i<=12; $i++){
			
				$html_output .= '<li><a href="'.$demofiles_data["previewlink-$i"].'" target="_blank" class="product '.$demofiles_data["id-$i"].'"></a>';
				$html_output .= '<div class="item-wrap">';
				$html_output .= '<h3>'.$demofiles_data["title-$i"].'</h3>';
				$html_output .= '<div class="importoptions"><span>'.__("Select what you'd like to import:", 'swift-importer').'</span><div class="dinput">';
				$html_output .= '<input type="checkbox" name="democontent'.$i.'" id="democontent'.$i.'">Demo Content</input></div><div class="dinput">';
				$html_output .= '<input type="checkbox" name="widgetsoption'.$i.'" id="widgetsoption'.$i.'">Widgets</input></div><div class="dinput">';
				$html_output .= '<input type="checkbox" name="themeoption'.$i.'" id="themeoption'.$i.'">Theme Options</input></div><div class="dinput">';
				$html_output .= '<input type="checkbox" name="coloroption'.$i.'" id="coloroption'.$i.'">Color Options</input></div></div>';
				$html_output .= '<div data-demoid="'.$i.'" data-url="'.$demourl.'&amp;demoid='.$i.'" class="demoimp-button">Import</div>';
				$html_output .= '</div></li>';
				
			}
			$html_output .= '</ul></div>';	
		}	
		echo $html_output;
		?>
		
	</div>
	<div class="sf-modal-notice">
		<div class="spinnermessage">
			<h3>Importing Demo</h3> 
			<p>Please be patient it could take a few minutes.</p>
						
			<div class="sf-progress-bar-wrapper html5-progress-bar">
			<p><span>Note</span>: If the progress indicator doesn't change for a couple minutes. Repeat the import process.</p>
            	<div class="progress-bar-wrapper">
                	<progress id="progressbar" value="0" max="100"></progress>
            	</div>
            	<div class="progress-value">0%</div>
            	<div class="progress-bar-message"></div>
            </div>
            <div id="sf_import_close">Close</div>
		</div>
	</div>
	<div  class="sf-black-overlay"></div>

	<?php	}

} 

add_action( 'wp_ajax_sf_import_content', 'sf_import_content' );
add_action( 'wp_ajax_sf_import_colors', 'sf_import_colors' );
add_action( 'wp_ajax_sf_import_options', 'sf_import_options' );
add_action( 'wp_ajax_sf_import_widgets', 'sf_import_widgets' );





function sf_import_widgets(){
			
			// include Widget data class
			if (!class_exists( 'Swift_Widget_Data' )) {
				require dirname( __FILE__ ) . '/class-widget-data.php';
			}
			
			$demoid =  $_POST['demo'] ;
			$swift_import = new Swift_Import();
			$widget_data = new Swift_Widget_Data();
			$demofiles_data = $swift_import->initialize_data();
			$widget_file = $demofiles_data['widgets-'.$demoid];
			$widget_data->ajax_import_widget_data($widget_file);
	
			wp_die(); 
	}
	
function sf_import_options(){
		
			$demoid =  $_POST['demo'] ;
			$swift_import = new Swift_Import();
			$demofiles_data = $swift_import->initialize_data();
			$file = $demofiles_data['themeoptions-'.$demoid];
			$import = file_get_contents( $file );		
			$imported_options = array();
		
			if ( !empty( $import ) ) {
                $imported_options = json_decode( htmlspecialchars_decode( $import ), true );
         	}
				
			$plugin_options['REDUX_imported'] = 1;
        	foreach($imported_options as $key => $value) {
					$plugin_options[$key] = $value;
        	}
			
			if(!empty($imported_options) && is_array($imported_options) && isset($imported_options['redux-backup']) && $imported_options['redux-backup'] == '1' ) {
            	 $plugin_options['REDUX_imported'] = 1;	 
             	foreach($imported_options as $key => $value) {
						  $plugin_options[$key] = $value;
             	}

             	/**
               	* action 'redux/options/{opt_name}/import'
               	* @param  &array [&$plugin_options, redux_options]
               	*/
					 
                do_action_ref_array( "redux/options/sf_atelier_options/import", array(&$plugin_options, $imported_options));

                $plugin_options['REDUX_COMPILER'] = time();
                unset( $plugin_options['defaults'], $plugin_options['compiler'], $plugin_options['import'], $plugin_options['import_code'] );
			    $swift_import->set_theme_options( $plugin_options );
        	}
		 
			update_option( 'sf_atelier_options', $plugin_options );    
		
			wp_die(); 
}
 
function sf_import_colors() {
	
			global $plugin_path;
			$demoid =  $_POST['demo'] ;
			$plugin_path = dirname(__FILE__);
			$swift_import = new Swift_Import();
			$demofiles_data = $swift_import->initialize_data();
			$file = $demofiles_data['colors-'.$demoid];
				
			$import = file_get_contents( $file );							
		
			if ( !empty( $import ) ) {
	        	$imported_options = json_decode( htmlspecialchars_decode( $import ), true );
        	}
			
			$sf_customizer_options = array();
			if( !empty( $imported_options ) && is_array( $imported_options ) )  {
               
                foreach($imported_options as $key => $value) {	
				
					$sf_customizer_options[$key] = $value;
					
                }
			}
		
			update_option('sf_customizer', $sf_customizer_options);
			
			wp_die(); 
}

function sf_import_content() {
			
			global $wpdb; // this is how you get access to the database
    		$sf_import = new Swift_Import();
    		
    		if ( !empty($_POST['xml']) )
				$file =  $_POST['xml'] ;
				
			if ( isset($_POST['demo']) )
				$demoid =  $_POST['demo'] ;
			
		    if ( !class_exists('WP_Importer') ) {
    		    ob_start();
            	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
            	require_once($class_wp_importer); 
    		} 
               
            require_once(dirname(__FILE__) . '/includes/class.wordpress-importer.php');
            $swift_import = new WP_Import();     
            set_time_limit(0);
            $swift_import->fetch_attachments = true;
            
            if ( isset($_POST['menus']) && $_POST['menus'] ){
             	$path = dirname(__FILE__) . '/demofiles/demo' .$demoid  . '/demo-' . $demoid . '-pagesmenus.xml.gz'; 	
            }
            else{
				$path = dirname(__FILE__) . '/demofiles/demo' .$demoid  . '/' . $file; 
			}
            
         	$returned_value = $swift_import->import($path);		
         	ob_get_clean();  
            if ( is_wp_error($returned_value) ){
                echo "An Error Occurred During Import";
            }
            else {
                echo  "Content imported successfully - " . $path;
                
                if ( isset($_POST['menus']) && $_POST['menus'] ){

					$sf_import->assign_menus_to_locations($demoid);
                	$static_frontpage = get_page_by_title( 'Home' );
					update_option( 'page_on_front', $static_frontpage->ID );
					update_option( 'show_on_front', 'page' );   
					
					if( $demoid == 5 ){
			
						$menu_special_data_id = $sf_import->get_menu_item_special_data_page("Wine Club"); 
		    			update_post_meta( $menu_special_data_id , '_menu_newbadge', 1 );	
		    
				    	$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Our Newsletter");     
				    	update_post_meta( $menu_special_data_id , '_menu_menuitembtn', 1 );	
			
	    			}	
                	
                	if( $demoid == 0 ){
                                			  	
            			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demos");
						update_post_meta( $menu_special_data_id , '_menu_megamenu', 1 );	
			    		update_post_meta( $menu_special_data_id , '_menu_megamenucols', 6 );	
			    		update_post_meta( $menu_special_data_id , '_menu_is_altstyle', 1 );	
		    			update_post_meta( $menu_special_data_id , '_menu_hideheadings', 1 );	
		    			update_post_meta( $menu_special_data_id , '_menu_newbadge', 1 );	
		 		  
		    			$demo1_text= '<a href="http://atelier.swiftideas.com/form-demo/"><img class="aligncenter size-medium wp-image-14352" src="http://atelier.swiftideas.com/wp-content/uploads/2014/09/selby-demo-300x230.jpg" alt="form-demo" width="300" height="230" /></a>
<h3 style="text-align: center;"><a href="http://atelier.swiftideas.com/form-demo/" target="_blank">FORM COFFEE CO.</a></h3>';

            			$demo2_text= '<a href="http://atelier.swiftideas.com/union-demo/"><img class="aligncenter size-medium wp-image-14354" src="http://atelier.swiftideas.com/wp-content/uploads/2014/09/union-demo-300x230.jpg" alt="union-demo" width="300" height="230" /></a>
<h3 style="text-align: center;"><a href="http://atelier.swiftideas.com/union-demo/" target="_blank">UNION BOUTIQUE</a></h3>';
  
            			$demo3_text= '<a href="http://atelier.swiftideas.com/convoy-demo/"><img class="aligncenter size-medium wp-image-14353" src="http://atelier.swiftideas.com/wp-content/uploads/2014/09/convoy-demo-300x230.jpg" alt="convoy-demo" width="300" height="230" /></a>
<h3 style="text-align: center;"><a href="http://atelier.swiftideas.com/convoy-demo/" target="_blank">CONVOY</a></h3>';
    
            			$demo4_text= '<a href="http://atelier.swiftideas.com/tilt-demo/"><img class="aligncenter size-medium wp-image-14355" src="http://atelier.swiftideas.com/wp-content/uploads/2014/09/tilt-demo-300x230.jpg" alt="tilt-demo" width="300" height="230" /></a><h3 style="text-align: center;"><a href="http://atelier.swiftideas.com/tilt-demo/" target="_blank">TILT ART PRINTS</a></h3>';
      
            			$demo5_text= '<a href="http://atelier.swiftideas.com/lab-demo/"><img class="aligncenter size-medium wp-image-14356" src="http://atelier.swiftideas.com/wp-content/uploads/2014/09/lab-demo-300x230.jpg" alt="lab-demo" width="300" height="230" /></a>
<h3 style="text-align: center;"><a href="http://atelier.swiftideas.com/lab-demo/" target="_blank">LAB WINE SHOP</a></h3>'; 

                		$demo6_text= '<br><br>[sf_button colour="black" type="sf-icon-reveal" size="standard" link="http://atelier.swiftideas.com/features/#demos" target="_self" icon="fa-long-arrow-right" dropshadow="no" extraclass=""]VIEW ALL[/sf_button]';

            			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demo 1");
		    			update_post_meta( $menu_special_data_id , '_menu_item_htmlcontent', $demo1_text );	
		    
		    			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demo 2");
		    			update_post_meta( $menu_special_data_id , '_menu_item_htmlcontent', $demo2_text );	
		  	
			    		$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demo 3");
			    		update_post_meta( $menu_special_data_id , '_menu_item_htmlcontent', $demo3_text );	
		  
			    		$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demo 4");
		    			update_post_meta( $menu_special_data_id , '_menu_item_htmlcontent', $demo4_text );	
		    
		    			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demo 5");
		    			update_post_meta( $menu_special_data_id , '_menu_item_htmlcontent', $demo5_text );	
		    	
		    			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Demo 6");
		    			update_post_meta( $menu_special_data_id , '_menu_item_htmlcontent', $demo6_text );	
		          
		    			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Shop Styles");
		    			update_post_meta( $menu_special_data_id , '_menu_megamenu', 1 );	
		    			update_post_meta( $menu_special_data_id , '_menu_megamenucols', 4 );	
		      
		    			$menu_special_data_id = $sf_import->get_menu_item_special_data_page("Features");
		    			update_post_meta( $menu_special_data_id , '_menu_megamenu', 1 );	
		    			update_post_meta( $menu_special_data_id , '_menu_megamenucols', 5 );	
		    			update_post_meta( $menu_special_data_id , '_menu_hideheadings', 1 );	
		    
		    			$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Pages");
		    			update_post_meta( $menu_special_data_id , '_menu_megamenu', 1 );	
		    			update_post_meta( $menu_special_data_id , '_menu_megamenucols', 6 );	
		    
			    		$menu_special_data_id = $sf_import->get_menu_item_special_data_custom("Elements");
			    		update_post_meta( $menu_special_data_id , '_menu_megamenu', 1 );	
		    			update_post_meta( $menu_special_data_id , '_menu_megamenucols', 5 );	
		    			update_post_meta( $menu_special_data_id , '_menu_hideheadings', 1 );
		    		}	    
		 		}
     		}
     
    		//ob_get_clean();  
			wp_die(); 
}

// Updater
require_once('wp-updates-plugin.php');
new WPUpdatesPluginUpdater_976( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));


//Message displayed when Atelier theme is not active
function admin_notice_message(){    		
		echo '<div class="updated"><p>Atelier theme must be instaled to use this plugin. The import functionalities are disabled.</p></div>';		
}

function swift_importer_menu_page(){
    add_menu_page( 'Atelier Demo Content', 'Atelier Demos', 'manage_options', 'admin.php?import=swiftdemo', '', plugin_dir_url(__FILE__).'/assets/images/logo.png');
}

add_action( 'admin_menu', 'swift_importer_menu_page' );

if ( ! defined( 'WP_LOAD_IMPORTERS' ) )
	return;

// include Widget data class
if (!class_exists( 'Swift_Widget_Data' )) {
	require dirname( __FILE__ ) . '/class-widget-data.php';
}

function swift_importer_init() {
		
	if (!function_exists('sf_atelier_setup')) {	
		add_action('admin_notices', 'admin_notice_message');   
	}
	
	load_plugin_textdomain( 'wordpress-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * WordPress Importer object for registering the import callback
	 * @global Swift_Import $Swift_Import
	 */
	$GLOBALS['Swift_Import'] = new Swift_Import();
		
	register_importer( 'swiftdemo', 'Atelier Demo Content', __("Import demo content for Atelier by Swift Ideas. 1 click and you're ready to go!  <strong>By Swift Ideas</strong>", 'wordpress-importer'), array( $GLOBALS['Swift_Import'], 'dispatch' ) );
	
define( "WIDGET_DATA_MIN_PHP_VER", '5.3.0' );

register_activation_hook( __FILE__, 'swift_widget_data_activation' );

function swift_widget_data_activation() {
	if ( version_compare( phpversion(), WIDGET_DATA_MIN_PHP_VER, '<' ) ) {
		die( sprintf( "The minimum PHP version required for this plugin is %s", WIDGET_DATA_MIN_PHP_VER ) );
	}
}

}

add_action( 'admin_init', 'swift_importer_init' ); 
add_action( 'admin_enqueue_scripts', 'swift_importer_scripts');

if (!function_exists('swift_importer_scripts')){
	function swift_importer_scripts(){		
			wp_enqueue_style( 'widget_data', plugins_url( '/assets/sf_importer.css', __FILE__ ) );
			wp_enqueue_script( 'widget_data', plugins_url( '/assets/sf_importer.js', __FILE__ ), array( 'jquery' ) );
		}
} 