<?php
/**
 * Live preview page
 */  
ob_start(); 
$absolute_path = __FILE__;
$path_to_file = explode( 'wp-content', $absolute_path );
$path_to_wp = $path_to_file[0];
require_once( $path_to_wp . '/wp-load.php' );
define( 'WP_USE_THEMES' , false);
?>
<!DOCTYPE HTML>
<html>
<head>
<?php wp_head(); ?>
<style>
@import url(http://fonts.googleapis.com/css?family=Open+Sans:700,600,400);
@import url('../assets/lib/font_awesome/css/font-awesome.min.css');
body { 
	background:#fff !important;
	font:14px/20px 'Open Sans', sans-serif;	
	padding:25px 40px 20px !important;
	margin:0 !important;
	height:auto !important;
}
body:before,
body:after { display:none; }

#go-pricing-preview { margin:0 auto; }
#go-pricing-forbidden {
	font-size:14px;
	height:50px;
	line-height:50px;
	text-align:center;	
}
#go-pricing-forbidden i {
	color:#fa5541;
	font-size:20px;	
	margin-right:7px;
	position:relative;
	top:3px;	
}
#go-pricing-preview .gw-go { margin-bottom:0 !important;}
</style>
</head>
<body>
<?php 
$instance = GW_GoPricing::instance();
if ( !is_user_logged_in() || empty( $_GET['id'] ) || empty( $_GET['nonce'] ) || ( !empty( $_GET['nonce'] ) &&  wp_verify_nonce( $_GET['nonce'], $instance['plugin_base'] . '-preview' ) === false ) ) :
	?>
	<div id="go-pricing-forbidden"><i class="fa fa-exclamation-triangle"></i><?php _e( 'Oops, Forbidden!', 'go_pricing_textdomain' ); ?></div>
	<?php 
else :
?>
<div id="go-pricing-preview"><?php echo do_shortcode( '[go_pricing postid="' . (int)$_GET['id'] . '" margin_bottom="0" preview="true"]' ); ?></div>
<?php 
endif;
wp_footer(); 
?>
</body>
</html>
<?php 
$html = ob_get_clean(); 
header( 'Content-Type: text/html; charset=utf-8' );
echo $html;
ob_end_flush();
?>
