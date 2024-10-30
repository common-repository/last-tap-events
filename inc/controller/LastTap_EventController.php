<?php
/**
 * @version 1.0
 *
 * @package LastTapEvents/inc/controller
 * @see LastTap_BaseController
 */

defined('ABSPATH') || exit;

class LastTap_EventController extends LastTap_BaseController

{
    private  $is_user_logged_in;

    public function lt_register()
    {

        $this->settings = new LastTap_SettingsApi();

        $this->callbacks = new LastTap_EventCallbacks();

        add_action('init', array($this, 'lt_eventposts'));
        // add_filter('the_content', array($this, 'lt_prepend_event_meta_to_content')); //gets our meta data and dispayed it before the content
        add_shortcode('events', array($this, 'lt_event_shortcode_output'));
        add_action('manage_event_posts_columns', array($this, 'lt_set_event_custom_columns'));
        add_action('manage_event_posts_custom_column', array($this, 'lt_set_event_custom_columns_data'), 10, 2);
        add_filter('manage_edit-event_sortable_columns', array($this, 'lt_set_event_custom_columns_sortable'));
        add_action('init', function(){ $this->is_user_logged_in = is_user_logged_in(); $this->is_user_logged_in; });
        add_filter( "views_edit-event", array($this, 'modified_views_event_detail') );

    }
    
    public function lt_eventposts()
    {
        /**
         * Enable the event custom post type
         * http://codex.wordpress.org/Function_Reference/register_post_type
         */
        //Labels for post type
        $labels = array(
            'name' => __('Event', 'last-tap-events'),
            'singular_name' => __('Event', 'last-tap-events'),
            'menu_name' => __('Events', 'last-tap-events'),
            'name_admin_bar' => __('Event', 'last-tap-events'),
            'add_new' => __('Add New', 'last-tap-events'),
            'add_new_item' => __('Add New Event', 'last-tap-events'),
            'new_item' => __('New Event', 'last-tap-events'),
            'edit_item' => __('Edit Event', 'last-tap-events'),
            'view_item' => __('View Event', 'last-tap-events'),
            'all_items' => __('All Events', 'last-tap-events'),
            'searlt_items' => __('Search Events', 'last-tap-events'),
            'parent_item_colon' => __('Parent Event:', 'last-tap-events'),
            'not_found' => __('No Event found.', 'last-tap-events'),
            'not_found_in_trash' => __('No Events found in Trash.', 'last-tap-events'),
        );
        //arguments for post type
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav' => true,
            'query_var' => true,
            'hierarchical' => true,
            'supports' => array('title', 'thumbnail', 'editor', 'excerpt'),
            'has_archive' => true,
            'menu_position' => 20,
            'show_in_admin_bar' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'rewrite' => array('slug' => 'event', 'with_front' => 'true')
        );
        //register post type
        register_post_type('event', $args);
    }

    
    //shortcode display
    public function lt_event_shortcode_output($atts, $content = '', $tag)
    {

        //build default arguments
        $arguments = shortcode_atts(array(
                'event_id' => '',
                'number_of_event' => -1)
           , $atts, $tag);

        //uses the main output function of the location class
        return $this->lt_get_event_output($arguments);

    }

   
    public function lt_get_event_output($arguments = "")
    {

        global $post;

        $wp_locale = new WP_Locale();

        add_image_size( 'event-thumb', 270, 175, false );

        //default args
        $default_args = array(
            'event_id' => '',
            'number_of_event' => -1
        );

        //update default args if we passed in new args
        if (!empty($arguments) && is_array($arguments)) {
            //go through each supplied argument
            foreach ($arguments as $arg_key => $arg_val) {
                //if this argument exists in our default argument, update its value
                if (array_key_exists($arg_key, $default_args)) {
                    $default_args[$arg_key] = $arg_val;
                }
            }
        }

        //find event
        $event_args = array(
            'post_type' => 'event',
            'posts_per_page' => $default_args['number_of_event'],
            'post_status' => 'publish',
            'meta_key' => '_lt_start_eventtimestamp',
            'orderby' => 'meta_value_num',

        );
        //if we passed in a single event to display
        if (!empty($default_args['event_id'])) {
            $event_args['include'] = $default_args['event_id'];
        }

        //output
        $html = '';
        $events = get_posts($event_args);

        //if we have event
        if ($events) {
            //foreach event
            foreach ($events as $key => $event) {
                                //title
                //collect event data
                $event_id = $event->ID;
                $event_title = get_the_title($event_id);
                $event_permalink = get_permalink($event_id);

                $html .= '<article class="lastTap col-lg-12 " style="border: 12px solid '.get_option( 'event_border_color').'">';
                $html .= '<div class="lastTap row">';

                $html .= '<h2 class="lastTap title">';
                $html .= '<a href="' . esc_url($event_permalink) . '" title="' . esc_attr__('view Event', 'last-tap-events') . '">';
                $html .= $event_title;
                $html .= '</a>';
                $html .= '</h2>';

                $html .= '<section class="lastTap col-lg-6 sermon" >';

                $event_thumbnail = get_the_post_thumbnail($event_id, 'event-thumb');
                $html .= '<div class="lastTap col-lg-1 image_content">';

                $event_content = apply_filters('the_content', $event->post_content);
                $html .= '</div>';

                if (!empty($event_content)) {
                    $event_content = strip_shortcodes(wp_trim_words($event_content, 40, '...'));
                }

                //(lets third parties hook into the HTML output to output data)
                $html = apply_filters('event_before_main_content', $html);

                //image & content
                if (!empty($event_thumbnail) || !empty($event_content)) {

                    if (!empty($event_thumbnail)) {
                        $html .= '<p class="lastTap col-lg-3 image_content">';
                        $html .= $event_thumbnail;

                        $html .= '</p>';
                    }else{
                    $html .= '<p class="lastTap col-lg-3 image_content">';
                    $html .= '<img src="' . $this->plugin_url . '/assets/images/no-image-available-icon-6.png">';
                    $html .= '</p>';


                    }
                    if (!empty($event_content)) {
                        $html .= '<img src="' . $this->plugin_url . '/assets/icon/compose.svg" style="width:20px; height:20px;"><strong>' . "\t\n" . __('Publish date:'."\t\n", 'last-tap-events') . '</strong>' . get_the_date('M d Y') . '<br>';

                        ?>
                        <body onload='verHora()'><h3 id='relogio'></h3></body><?php


                        // Gets the event start month from the meta field
                        $month = get_post_meta($event_id, '_event_detall_info', true)['_lt_start_month'];
                        // Converts the month number to the month name
                        $month = $wp_locale->get_month_abbrev($wp_locale->get_month($month));
                        // Gets the event start day
                        $day = get_post_meta($event_id, '_event_detall_info', true)['_lt_start_day'];
                        // Gets the event start year
                        $year = get_post_meta($event_id, '_event_detall_info', true)['_lt_start_year'];
                        $startEvent = get_post_meta($event_id, '_event_detall_info', true)['_lt_start_eventtimestamp'];
                        $endEvent = get_post_meta($event_id, '_event_detall_info', true)['_lt_end_eventtimestamp'];


                        $html .= '<img src="' . $this->plugin_url . '/assets/icon/clock.svg" style="width:20px; height:20px;"><strong>' . "\t\n" . __('Event start date:', 'last-tap-events') . '</strong>' . "\t\n" . $month . ' ' . $day . ' ' . $year . '<br>';

                        $html .= '<img src="' . $this->plugin_url . '/assets/icon/timestampdate.svg" style="width:20px; height:20px;"><strong>' . "\t\n" . __('Start event timestamp:', 'last-tap-events') . '</strong>' . "\t\n" . $this->callbacks->formatDate($startEvent) . '<br>';

                        $html .= '<img src="' . $this->plugin_url . '/assets/icon/finish.svg" style="width:20px; height:20px;"><strong>' . "\t\n" . __('End event timestamp:', 'last-tap-events') . '</strong>' . "\t\n" . $this->callbacks->formatDate($endEvent) . '<br>';
                    $html .= '</section>';
                    $html .= '<div class="lastTap col-lg-6">';
                    $html .= $event_content;
                    $html .= '</div>';
                    }

                }

                //apply the filter after the main content, before it ends
                //(lets third parties hook into the HTML output to output data)
                $html = apply_filters('event_after_main_content', $html);

                //readmore
                $html .= '<a class="lastTap link" href="' . esc_url($event_permalink) . '" title="' . esc_attr__('view Event', 'last-tap-events') . '">' . __('View Event', 'last-tap-events') . '</a>';
            $html .= '</section>';
            $html .= '</article>';
            $html .= '<div class="lastTap cf"></div>';

            }// and foreach
        } // and if

        return $html;
    }

    public function get_participe_event_form(){
                
                echo  do_shortcode( '[particip-form]', false );

    }

    public function lastTap_count_event_participant($event_id){
        $all_post_ids = get_posts(
            array(
                'fields'            => 'post_id',
                'posts_per_page'    => -1,
                'post_type'         => 'participant'
            )
        );
        $count_participant = [];
            foreach ($all_post_ids as $k => $v) {
                $count = get_post_meta( $v->ID, '_event_participant_key', false );
                    foreach ($count as $key => $value) {
                        if($value['post_event_id'] == $event_id && $value['approved'] == 1){
                            $count_participant[] = $value['post_event_id'];
                        }
                    }               
                }
        return $count_participant;


    }

    public function lt_set_event_custom_columns($columns)
    {
        $title = $columns['title'];
        $date = $columns['date'];
        unset($columns['title'], $columns['date']);

        $columns['title'] = __( 'Event name', 'last-tap-events');
        $columns['telephone'] =  __('Telephone', 'last-tap-events');
        $columns['price'] =  __('Price', 'last-tap-events');
        $columns['email'] =  __('Event Organizers email', 'last-tap-events');
        $columns['location'] =  __('Location', 'last-tap-events');
        $columns['data'] = __('Date and Time', 'last-tap-events');

        return $columns;
    }

    public function lt_set_event_custom_columns_data($column, $post_id)
    {
        $_event_detall_info = get_post_meta($post_id, '_event_detall_info', true);

        $corrency = esc_attr( get_option( 'event_currency' ) );
        $speakers = esc_attr( get_option( 'event_speakers' ) );
        // $title = isset($title['name']) ? $title['name'] : '';
        $email = isset($_event_detall_info['_lt_event_organizer']) ? $_event_detall_info['_lt_event_organizer'] : '';
        $telephone = isset($_event_detall_info['_lt_event_phone']) ? $_event_detall_info['_lt_event_phone'] : '';
        $price = isset($_event_detall_info['_lt_event_price']) ? $_event_detall_info['_lt_event_price'] : '00.00';
        $startEvent = isset($_event_detall_info['_lt_start_eventtimestamp']) ? $_event_detall_info['_lt_start_eventtimestamp'] : '00:00';
        $andEvent = isset($_event_detall_info['_lt_end_eventtimestamp']) ? $_event_detall_info['_lt_end_eventtimestamp'] : '00:00';
        $_lt_event_street = isset($_event_detall_info['_lt_event_street']) ? $_event_detall_info['_lt_event_street'] : '00:00';
        $_lt_event_address = isset($_event_detall_info['_lt_event_address']) ? $_event_detall_info['_lt_event_address'] : '00:00';
        $_lt_event_city = isset($_event_detall_info['_lt_event_city']) ? $_event_detall_info['_lt_event_city'] : '00:00';

        $corrency = isset($corrency) ? $corrency : '';
        $speakers = isset($speakers) ? $speakers : 0;

        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['baseurl'] . '/2019/12/wp-header-logo.png' ;
        $a =preg_replace('/^https?:/', '', $upload_dir);

        switch ($column) {
            case 'title':
                echo esc_html('<strong>' . $title . '</strong><br/><a href="mailto:' . $email . '">' . $email . '</a>');
                break;

            case 'telephone':
                echo esc_html($telephone);
                break;
            case 'price':
                echo esc_html($price . ' '.$corrency);
                break;

            case 'email':
                echo esc_html($email);
                break;
            case 'location':
                echo "<strong>" .$_lt_event_street . ' ' .  $_lt_event_address . "</strong><p>". ' ' . $_lt_event_city . '</p>';
                break;
            case 'data':
                echo wp_kses_post( $this->callbacks->formatDate($startEvent, "F j Y" ) . ' - ' . $this->callbacks->formatDate($andEvent,  "F j Y") . '<p>'. __('Time:', 'last-tap-events'). $this->callbacks->formatDate($startEvent, "H:i" ) . ' - ' . $this->callbacks->formatDate($andEvent,  "H:i") . '</p>');
                break;
        }
    }

    public function lt_set_event_custom_columns_sortable($columns)
    {
        $columns['title'] = __( 'name', 'last-tap-events');
        $columns['telephone'] = __( 'Telephone', 'last-tap-events');
        $columns['price'] = __( 'price', 'last-tap-events');
        $columns['email'] = __( 'Email', 'last-tap-events');
        $columns['location'] = __( 'Location', 'last-tap-events');
        $columns['data'] = __( 'Date and Time', 'last-tap-events');

        return $columns;
    }

    function modified_views_event_detail( $views ) 
    {
        $views['all'] = str_replace( 'All ', 'All Events ', $views['all'] );

        if(!empty($views['publish'])){
             $views['publish'] = str_replace( 'Published ', __('Event Published ', 'last-tap-events'), $views['publish'] );
        }
        return $views;
    }
}