<?php

    /*
    *
    *	Swift Page Builder - Asset Functions Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */


    /* AJAXURL GLOBAL
    ================================================== */
    if ( !function_exists('spb_ajaxurl_global') ) {
		function spb_ajaxurl_global() {
		?>
			<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			</script>
			<?php
		}
		add_action('wp_head','spb_ajaxurl_global');
	}


    /* CONTAINER OVERLAY
    ================================================== */
	if ( !function_exists('spb_container_overlay') ) {
		function spb_container_overlay() {
				$preloader = "";
				if ( function_exists( 'sf_get_preloader_svg') ) {
					$preloader = sf_get_preloader_svg( true );
				}
			?>

			<div class="sf-container-overlay">
				<div class="sf-loader">
					<?php echo $preloader; ?>
				</div>
			</div>

		<?php }
		add_action( 'wp_footer', 'spb_container_overlay' );
	}


    /* TEAM MEMBER AJAX
    ================================================== */
    if ( !function_exists('spb_team_member_ajax') ) {
		function spb_team_member_ajax() {

			$postID = '';

			if ( ! empty( $_REQUEST['post_id'] ) ) {
	            $postID = $_REQUEST['post_id'];
	        }

	        $args = array(
	        	'p' 		=> $postID,
	        	'post_type' => 'team'
	        );
	        $query  = new WP_Query($args);

		    if ($query->have_posts()) {
		        while ( $query->have_posts() ) {
		            $query->the_post();

		            $member_name     	= get_the_title();
		            $member_position 	= sf_get_post_meta( $postID, 'sf_team_member_position', true );
	                $custom_excerpt  	= sf_get_post_meta( $postID, 'sf_custom_excerpt', true );
	                $member_email       = sf_get_post_meta( $postID, 'sf_team_member_email', true );
				    $member_phone       = sf_get_post_meta( $postID, 'sf_team_member_phone_number', true );
				    $member_twitter     = sf_get_post_meta( $postID, 'sf_team_member_twitter', true );
				    $member_facebook    = sf_get_post_meta( $postID, 'sf_team_member_facebook', true );
				    $member_linkedin    = sf_get_post_meta( $postID, 'sf_team_member_linkedin', true );
				    $member_skype       = sf_get_post_meta( $postID, 'sf_team_member_skype', true );
				    $member_google_plus = sf_get_post_meta( $postID, 'sf_team_member_google_plus', true );
				    $member_instagram   = sf_get_post_meta( $postID, 'sf_team_member_instagram', true );
				    $member_dribbble    = sf_get_post_meta( $postID, 'sf_team_member_dribbble', true );

				    $unfiltered_content = str_replace( '<!--more-->', '', $query->post->post_content );
					$filtered_content   = apply_filters( 'the_content', $unfiltered_content );
	                $member_bio 	 	= $filtered_content;
	                $member_image_url   = wp_get_attachment_url( get_post_thumbnail_id(), 'full' );

		            $data = '
		                <div class="team-member-ajax-content">
		                	<a href="#" class="team-ajax-close">&times;</a>
		                	<figure class="profile-image-wrap">
			                	<div class="inner-wrap">';
									if ( $member_image_url != "" ) {
									$data .= '<img itemprop="image" src="' . esc_url( $member_image_url ) . '" alt="' . $member_name . '"/>';
									}
						        $data .= '<h1 class="entry-title">' . $member_name . '</h1>
						        			<h3 class="entry-subtitle">' . $member_position . '</h3>
						        </div>
					        	<div class="backdrop" style="background-image: url(' . esc_url( $member_image_url ) . ');"></div>
					        </figure>
					        <div class="content-wrap">
			                    <div class="entry-content">' . do_shortcode($member_bio) . '</div>
		                	</div>
		                </div>
		                <footer class="team-member-aux">
		                		<div class="member-aux-inner clearfix">
			                		<ul class="member-contact">';
				                        if ( $member_phone ) {
				                            $data .= '<li class="phone"><span itemscope="telephone">' . esc_attr($member_phone) . '</span>
				                            </li>';
				                        }
				                        if ( $member_email ) {
				                            $data .= '<li class="email"><span itemscope="email"><a href="mailto:' . sanitize_email($member_email) . '">' . $member_email . '</a></span>
				                            </li>';
				                        }
				                    $data .= '</ul>
			                		<ul class="social-icons">';
				                        if ( $member_twitter ) {
				                        	$data .= '<li class="twitter"><a href="http://www.twitter.com/' . esc_attr($member_twitter) . '" target="_blank"><i class="fa-twitter"></i><i class="fa-twitter"></i></a>
				                            </li>';
				                       	}
				                       	if ( $member_facebook ) {
				                            $data .= '<li class="facebook"><a href="' . esc_url($member_facebook) . '>" target="_blank"><i class="fa-facebook"></i><i class="fa-facebook"></i></a></li>';
				                        }
				                        if ( $member_linkedin ) {
				                            $data .= '<li class="linkedin"><a href="' . esc_url($member_linkedin) . '" target="_blank"><i class="fa-linkedin"></i><i class="fa-linkedin"></i></a></li>';
				                       	}
				                       	if ( $member_google_plus ) {
				                            $data .= '<li class="googleplus"><a href="' . esc_url($member_google_plus) . '" target="_blank"><i class="fa-google-plus"></i><i class="fa-google-plus"></i></a></li>';
				                        }
				                        if ( $member_skype ) {
				                            $data .= '<li class="skype"><a href="skype:' . esc_attr($member_skype) . '" target="_blank"><i class="fa-skype"></i><i class="fa-skype"></i></a></li>';
				                      	}
				                      	if ( $member_instagram ) {
				                            $data .= '<li class="instagram"><a href="' . esc_url($member_instagram) . '" target="_blank"><i class="fa-instagram"></i><i class="fa-instagram"></i></a></li>';
				                        }
				                        if ( $member_dribbble ) {
				                            $data .= '<li class="dribbble"><a href="http://www.dribbble.com/' . esc_attr($member_dribbble) . '" target="_blank"><i class="fa-dribbble"></i><i class="fa-dribbble"></i></a></li>';
				                    	}
				                    $data .= '</ul>
			                    </div>
		                	</footer>
		            </div>  
		            ';

		        }
		    } 
		    else {
		        $data = __( "Couldn't find team member, please try again.", "swift-framework-admin" );
		    }
		    wp_reset_postdata();

		    echo '<div id="postdata">'.$data.'</div>';

		    die();
		}
		add_action( 'wp_ajax_nopriv_spb_team_member_ajax', 'spb_team_member_ajax' );
		add_action( 'wp_ajax_spb_team_member_ajax', 'spb_team_member_ajax' );
	}

