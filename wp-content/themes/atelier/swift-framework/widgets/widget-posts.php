<?php

    /*
    *
    *	Custom Posts Widget
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    // Register widget
    add_action( 'widgets_init', 'init_sf_recent_posts' );
    function init_sf_recent_posts() {
        return register_widget( 'sf_recent_posts' );
    }

    class sf_recent_posts extends WP_Widget {
        function sf_recent_posts() {
            parent::__construct( 'sf_recent_custom_posts', $name = 'Swift Framework Recent Posts' );
        }

        function widget( $args, $instance ) {
            global $post;
            extract( $args );

            // Widget Options
            $title    = apply_filters( 'widget_title', $instance['title'] ); // Title
            $number   = $instance['number']; // Number of posts to show
            $category = $instance['category']; // Category to show

            if ( $category == "All" ) {
                $category = "all";
            }
            if ( $category == "all" ) {
                $category = '';
            }
            $category_slug = str_replace( '_', '-', $category );

            echo $before_widget;

            if ( $title ) {
                echo $before_title . $title . $after_title;
            }

            $video_icon = apply_filters( 'sf_video_icon' , '<i class="ss-video"></i>' );
            $audio_icon = apply_filters( 'sf_audio_icon' , '<i class="ss-music"></i>' );
            $picture_icon = apply_filters( 'sf_picture_icon' , '<i class="ss-picture"></i>' );
            $post_icon = apply_filters( 'sf_post_icon' , '<i class="ss-file"></i>' );

            $recent_posts = new WP_Query(
                array(
                    'post_type'      => 'post',
                    'posts_per_page' => $number,
                    'category_name'  => $category_slug,
                )
            );

            if ( $recent_posts->have_posts() ) :

                ?>

                <ul class="recent-posts-list">

                    <?php while ( $recent_posts->have_posts() ) : $recent_posts->the_post();

                        $thumb_type      = sf_get_post_meta( $post->ID, 'sf_thumbnail_type', true );
                        $post_title      = get_the_title();
                        $post_author     = get_the_author_link();
                        $post_date       = get_the_date();
                        $post_categories = get_the_category_list();
                        $post_comments   = get_comments_number();
                        $post_permalink  = get_permalink();
                        $thumb_image     = get_post_thumbnail_id();
                        $thumb_img_url   = wp_get_attachment_url( $thumb_image, 'widget-image' );
                        $image           = sf_aq_resize( $thumb_img_url, 94, 75, true, false );
                        $image_alt       = esc_attr( sf_get_post_meta( $thumb_image, '_wp_attachment_image_alt', true ) );
                        ?>
                        <li>
                            <a href="<?php echo esc_url($post_permalink); ?>" class="recent-post-image">
                                <?php if ( $image ) { ?>
                                    <img src="<?php echo esc_url($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>"
                                         height="<?php echo esc_attr($image[2]); ?>" alt="<?php echo esc_attr($image_alt); ?>"/>
                                <?php } else if ( $thumb_type == "video" ) { ?>
                                    <?php echo $video_icon; ?>
                                <?php } else if ( $thumb_type == "audio" ) { ?>
                                    <?php echo $audio_icon; ?>
                                <?php } else if ( $thumb_type == "slider" ) { ?>
                                    <?php echo $picture_icon; ?>
                                <?php } else { ?>
                                    <?php echo $post_icon; ?>
                                <?php } ?>
                            </a>

                            <div class="recent-post-details">
                                <a class="recent-post-title" href="<?php echo esc_url($post_permalink); ?>"
                                   title="<?php echo esc_attr($post_title); ?>"><?php echo esc_attr($post_title); ?></a>
                                <span><?php printf( __( 'By %1$s on %2$s', 'swiftframework' ), $post_author, $post_date ); ?></span>

                                <div class="comments-likes">
                                    <?php if ( comments_open() ) { ?>
                                        <div class="comments-wrapper">
                                            <a href="<?php echo esc_url($post_permalink); ?>#comment-area"><?php echo apply_filters( 'sf_comments_icon', '<i class="ss-chat"></i>' ); ?><span><?php echo esc_attr($post_comments); ?></span></a>
                                        </div>
                                    <?php } ?>
                                    <?php if ( function_exists( 'lip_love_it_link' ) ) {
                                        echo lip_love_it_link( get_the_ID(), false );
                                    } ?>
                                </div>
                            </div>
                        </li>

                        <?php wp_reset_query(); endwhile; ?>
                </ul>

            <?php endif; ?>

            <?php

            echo $after_widget;
        }

        /* Widget control update */
        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            $instance['title']    = strip_tags( $new_instance['title'] );
            $instance['number']   = strip_tags( $new_instance['number'] );
            $instance['category'] = strip_tags( $new_instance['category'] );

            return $instance;
        }

        /* Widget settings */
        function form( $instance ) {

            // Set defaults if instance doesn't already exist
            if ( $instance ) {
                $title    = $instance['title'];
                $number   = $instance['number'];
                $category = $instance['category'];
            } else {
                // Defaults
                $title    = '';
                $number   = '5';
                $category = '';
            }

            // The widget form
            ?>
            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo __( 'Title:', 'swiftframework' ); ?></label>
                <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"
                       name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>"
                       class="widefat"/>
            </p>
            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>"><?php echo __( 'Number of posts to show:', 'swiftframework' ); ?></label>
                <input id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>"
                       name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="text"
                       value="<?php echo esc_attr($number); ?>" size="3"/>
            </p>
            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id( 'category' )); ?>"><?php _e( 'Category', 'swiftframework' ); ?></label>
                <select name="<?php echo esc_attr($this->get_field_name( 'category' )); ?>"
                        id="<?php echo esc_attr($this->get_field_id( 'category' )); ?>" class="">
                    <?php
                        $options = sf_get_category_list( 'category' );
                        foreach ( $options as $option ) {
                            echo '<option value="' . $option . '" id="' . $option . '"', $category == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                        }
                    ?>
                </select>
            </p>
            </p>
        <?php
        }

    }

?>
