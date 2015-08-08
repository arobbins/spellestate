<?php

    /*
    *
    *	Swift Framework Menu Functions
    *	------------------------------------------------
    *	Swift Framework v3.0
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    *	sf_setup_menus()
    *
    */


    /* CUSTOM MENU SETUP
    ================================================== */
    register_nav_menus( array(
        'main_navigation' => __( 'Main Menu', "swiftframework" ),
        'overlay_menu'    => __( 'Overlay Menu', "swiftframework" ),
        'mobile_menu'     => __( 'Mobile Menu', "swiftframework" ),
        'top_bar_menu'    => __( 'Top Bar Menu', "swiftframework" ),
        'footer_menu'     => __( 'Footer Menu', "swiftframework" )
    ) );
    
    
    /* SLIDEOUT MENU SETUP
    ================================================== */
    if ( sf_theme_supports( 'slideout-menu' ) ) {
	    register_nav_menus( array(
	        'slideout_menu'   => __( 'Slideout Menu', "swiftframework" ),
	    ) );
    }


?>
