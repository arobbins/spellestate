<?php

    /*
    *
    *	Custom Instagram Widget
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class sf_instagram_widget extends WP_Widget {

        function sf_instagram_widget() {
            $widget_ops = array(
                'classname'   => 'instagram-widget',
                'description' => 'Show off your favorite Instagram photos'
            );
            parent::__construct( 'instagram-widget', 'Swift Framework Instagram Widget', $widget_ops );
        }

        function form( $instance ) {

            $instance   = wp_parse_args( (array) $instance, array(
                    'title'      => 'Instagram',
                    'number'     => 8,
                    'instagram_id' => '',
                    'instagram_token'  => ''
                ) );
            $title      = esc_attr( $instance['title'] );
            $instagram_id = $instance['instagram_id'];
            $instagram_token  = $instance['instagram_token'];
            $number     = absint( $instance['number'] );
            ?>
            <p>
                <label
                    for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'swiftframework' ); ?>
                    :</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo $title; ?>"/>
            </p>

            <p>
                <label
                    for="<?php echo $this->get_field_id( 'instagram_id' ); ?>"><?php _e( 'Instagram ID', 'swiftframework' ); ?>
                    :</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'instagram_id' ); ?>"
                       name="<?php echo $this->get_field_name( 'instagram_id' ); ?>" type="text"
                       value="<?php echo $instagram_id; ?>"/>
                <small>You can find your instagram ID here - <a href="http://jelled.com/instagram/lookup-user-id" target="_blank">http://jelled.com/instagram/lookup-user-id</a> You will also need to enter your token below.</small>
            </p>

			<p>
			    <label
			        for="<?php echo $this->get_field_id( 'instagram_token' ); ?>"><?php _e( 'Instagram Token', 'swiftframework' ); ?>
			        :</label>
			    <input class="widefat" id="<?php echo $this->get_field_id( 'instagram_token' ); ?>"
			           name="<?php echo $this->get_field_name( 'instagram_token' ); ?>" type="text"
			           value="<?php echo $instagram_token; ?>"/>
			    <small>You can generate your instagram access token here - <a href="http://www.pinceladasdaweb.com.br/instagram/access-token/" target="_blank">http://www.pinceladasdaweb.com.br/instagram/access-token/</a>. NOTE: This is REQUIRED.</small>
			</p>
			
            <p>
                <label
                    for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of Photos', 'swiftframework' ); ?>
                    :</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>"
                       name="<?php echo $this->get_field_name( 'number' ); ?>" type="text"
                       value="<?php echo $number; ?>"/>
            </p>

        <?php
        }

        function update( $new_instance, $old_instance ) {

            $instance = $old_instance;

            $instance['title']      = strip_tags( $new_instance['title'] );
            $instance['instagram_id'] = $new_instance['instagram_id'];
            $instance['instagram_token']  = $new_instance['instagram_token'];
            $instance['number']     = $new_instance['number'];

            return $instance;
        }

        function widget( $args, $instance ) {

            extract( $args );

            $title    	   = apply_filters( 'widget_title', $instance['title'] );
            $instagram_id  = $instance['instagram_id'];
            $instagram_token  = $instance['instagram_token'];
            $count     	   = $instance['number'];
            $widget_id 	   = "sf-instagram-widget-" . rand();

            if ( $title ) {
                echo $before_title . $title . $after_title;
            }

            echo $before_widget;
            ?>

            <ul id="<?php echo $widget_id; ?>" class="instagram_images clearfix"></ul>

            <script type="text/javascript">
                jQuery( document ).ready(
                    function() {
                    	var instagrams = jQuery('#<?php echo $widget_id; ?>'),
                    		count = parseInt( <?php echo $count; ?>, 10 );
                    	jQuery.ajax({
                    		type: "GET",
                    		dataType: "jsonp",
                    		cache: false,
                    		url: "https://api.instagram.com/v1/users/<?php echo $instagram_id; ?>/media/recent/?access_token=<?php echo $instagram_token; ?>",
                    		success: function(data) {
                    			for (var i = 0; i < count; i++) {
                    				if (data.data[i]) {
                    					var caption = "";
                    					if (data.data[i].caption) {
                    						caption = data.data[i].caption.text;
                    					}
                    					instagrams.append("<li class='instagram-item' data-date='"+data.data[i].created_time+"'><figure class='animated-overlay'><a target='_blank' href='" + data.data[i].link +"'></a><img class='instagram-image' src='" + data.data[i].images.low_resolution.url +"' width='306px' height='306px' /><figcaption><div class='thumb-info'><i class='fa-instagram'></i></div></figcaption></figure></li>");  
                    				} 
                    			}
                    		}
                    	});
                    }
                );
            </script>
            <?php

            echo $after_widget;
        }

    }

    add_action( 'widgets_init', 'sf_load_instagram_widget' );

    function sf_load_instagram_widget() {
        register_widget( 'sf_instagram_widget' );
    }

?>