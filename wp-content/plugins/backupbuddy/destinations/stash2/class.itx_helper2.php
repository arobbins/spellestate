<?php
require_once( dirname( __FILE__ ) . '/class.itcred.php' );

class ITXAPI_Helper2 {
	
	const API_URL = 'https://stash-api.ithemes.com';
	
	static function phpass_hash_password($password)
	    {
	        require_once( ABSPATH . WPINC . '/class-phpass.php');
	        
	        $hasher = new PasswordHash(8, true);
	        
	        echo 'In: ' . $password . '<br>';
	        $password = 'f5e3e518174aa84b2df10c0ff7d45444c62225b0bb3acadf988e682208649d5fjwooleyhttp://jordan.dev.ithemes.com4.1';
            $hash = $hasher->HashPassword($password);
            echo '<br>Out: ' . $hash . '<br>';
            
            return $hash;
	
        }
        
    //-----------------------------------------------------------------------------
    
        static function get_wordpress_phpass($user, $pass, $site, $wp)
        {                        
            
            require_once( ABSPATH . WPINC . '/class-phpass.php');
            
            $source_string = $pass . $user . $site . $wp;
            
            $salted_string2 = substr( $source_string, 0, max( strlen( $pass ), 512 ) );     //  new auth with hashed passwords
                        
            
            return self::phpass_hash_password( $salted_string2 );
    
        }
        
        
        /* remote_post()
         *
         * string on failure, else array of json decoded response
         *
         */
        static function remote_post( $url_params, $params = array() ) {
			$post_url = self::API_URL . '/?' . http_build_query( $url_params, null, '&' );
			
			$response = wp_remote_post(
				$post_url,
				array(
					'method' => 'POST',
					'timeout' => 15,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'body' => $params,
					'cookies' => array()
				)
			);
			
			if ( is_wp_error( $response ) ) {
				return 'Error #3892774: `' . $response->get_error_message() . '`.';
			} else {
				if ( null !== ( $response_decoded = json_decode( $response['body'], true  ) ) ) {
					return $response_decoded;
				} else {
					return 'Error #8393833: Unexpected server response: `' . htmlentities( $response['body'] ) . '`.';
				}
			}
        } // End remote_post().
        /*
        if ( is_wp_error( $response ) ) {
			return self::_error( 'Error #9037: Unable to connect to remote server or unexpected response. Details: `' . $response->get_error_message() . '` - URL: `' . $remoteAPI['siteurl'] . '`.' );
        */
	
} // End class.