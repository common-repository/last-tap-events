<?php

/* 
Template Name: HomeEventTemplate
*/

get_header(); ?>

          <?php $the_posts = get_posts(array(
            'posts_per_page' => 1,
            'post_type' => 'event',
            'order' => 'DESC'
          ));
                $data = array();
                foreach ($the_posts as $key => $value) {
                        $post_id = $value->ID;

                        $event_title = get_the_title($post_id);
                        $event_permalink = get_permalink($post_id);
                        $style = get_option( 'event_style_html' ) ?? 'class="lastTap img-responsive" alt="Event Image"';
                        $event_thumbnail = get_the_post_thumbnail($post_id, 'post-thumbnail', array( 'class' => 'lastTap img-responsive', 'alt'=> 'Event Image') );
                        $event_content = apply_filters('the_content', $value->post_content);
                        $event_content = strip_shortcodes(wp_trim_words($event_content, 3, '...'));

                        $mm =  get_post_meta( $post_id, '_event_detall_info', true )['_lt_start_month'];
                        $dd =  get_post_meta( $post_id, '_event_detall_info', true )['_lt_start_day'];
                        $yyy =  get_post_meta( $post_id, '_event_detall_info', true )['_lt_start_year'];
                        $start =  get_post_meta( $post_id, '_event_detall_info', true )['_lt_start_eventtimestamp'];
                        $end =  get_post_meta( $post_id, '_event_detall_info', true )['_lt_end_eventtimestamp'];
                        $current_time = $end;
                        list($end_year, $end_month, $end_day, $hour, $minute) = preg_split('([^0-9])', $current_time);
                        $current_timestamp = $end_year . '-' . $end_month . '-' . $end_day . ' ' . $hour . ':' . $minute;?>


    <?php } ?>

<?php 
get_footer();