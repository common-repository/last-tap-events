<?php

/**
 * @version 1.4
 *
 * @package LastTapEvents/inc/EMetaBox
 * @see LastTap_BaseController
 */

defined('ABSPATH') || exit;


class EventMetaBox{

	private $callbacks;
	public function __construct(){
        $this->callbacks = new LastTap_EventCallbacks();

        add_action('pre_get_posts', array($this, 'lt_event_query'));
		add_action('admin_init', array($this, 'lt_eventposts_metaboxes'));
        add_action('save_post', array($this, 'lt_eventposts_save_meta'), 1, 2);

	}
	/**
     * Adds event post metaboxes for start time and end time
     * http://codex.wordpress.org/Function_Reference/add_meta_box
     *
     * We want two time event metaboxes, one for the start time and one for the end time.
     * Two avoid repeating code, we'll just pass the $identifier in a callback.
     * If you wanted to add this to regular posts instead, just swap 'event' for 'post' in add_meta_box.
     */
    public function lt_eventposts_metaboxes()
    {
        add_meta_box('event_date_start', __( 'Start Date and Time', 'last-tap-events'), array($this, 'event_date'), 'event', 'side', 'default', array('id' => '_start'));
        add_meta_box('event_date_end', __('End Date and Time', 'last-tap-events'), array($this, 'event_date'), 'event', 'side', 'default', array('id' => '_end'));

        add_meta_box(
            'event_location',
            __( 'Event Location', 'last-tap-events'),
            array($this, 'event_location'),
            'event', 'normal',
            'default', array('id' => '_end'));
    }

    // Metabox HTML
    public function event_date($post, $args)
    {
        /*
         attribute prefix form name
         prefix _lt
        */

        $metabox_id = '_lt'. $args['args']['id'];

        global $post, $wp_locale, $postEvent;

        $postEvent = $post;
        // Use nonce for verification
        wp_nonce_field(plugin_basename(__FILE__), 'lt_eventposts_nonce');
        $time_adj = current_time('timestamp');
        $_event_detall_infos = get_post_meta($post->ID, '_event_detall_info', false);

            $month = gmdate('m', $time_adj);
            $day = gmdate('d', $time_adj);
            $year = gmdate('Y', $time_adj);
            $hour = gmdate('H', $time_adj);
            $min = '00';
 foreach ($_event_detall_infos as $key => $_event_detall_info) {
         

        $month = $_event_detall_info[$metabox_id.'_month'];
        
        $day = $_event_detall_info[$metabox_id.'_day'];
        
        $year = $_event_detall_info[$metabox_id.'_year'];
        
        $hour = $_event_detall_info[$metabox_id.'_hour'];

        $min = $_event_detall_info[$metabox_id.'_minute'];

    }
        echo '<div class="wrap">';
        $month_s = '<select name="' . $metabox_id . '_month">';
        for ($i = 1; $i < 13; $i = $i + 1) {
            $month_s .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
            if ($i == $month)
                $month_s .= ' selected="selected"';
            $month_s .= '>' . $wp_locale->get_month_abbrev($wp_locale->get_month($i)) . "</option>\n";
        }
        $month_s .= '</select>';
        echo $month_s;
        echo '<input class="small-text" type="number" step="1" min="1" name="' . $metabox_id . '_day" value="' . intval($day). '" size="2"  />';
        echo '<input class="small-text" type="number" step="1" min="1" name="' . $metabox_id . '_year" value="' . intval($year) . '" size="4"  /> @ ';
        echo '<input class="small-text" type="number" step="1" min="0" name="' . $metabox_id . '_hour" value="' . intval($hour) . '" size="2" />:';
        echo '<input class="small-text" type="number" step="1" min="0" name="' . $metabox_id . '_minute" value="' . intval($min). '" size="2"  />';
        echo '</div>';

    }

    public function event_location()
    {
        global $post;
        // Use nonce for verification
        wp_nonce_field(plugin_basename(__FILE__), 'lt_eventposts_nonce');
        // The metabox HTML
        $event_country = get_post_meta($post->ID, '_lt_event_country', true);
        $event_city = get_post_meta($post->ID, '_lt_event_city', true);
        $event_address = get_post_meta($post->ID, '_lt_event_address', true);
        $event_email = get_post_meta($post->ID, '_lt_event_email', true);
        $event_organizer = get_post_meta($post->ID, '_lt_event_organizer', true);
        $event_phone = get_post_meta($post->ID, '_lt_event_phone', true);
        $event_phone_2 = get_post_meta($post->ID, '_lt_event_phone_2', true);
        $event_street = get_post_meta($post->ID, '_lt_event_street', true);
        $event_partici = get_post_meta($post->ID, '_lt_event_partic_limit', true); 
        $event_price = get_post_meta($post->ID, '_lt_event_price', true); 

        $detal = get_post_meta($post->ID, '_event_detall_info', true); ?>


    <div class="wrap">
        <form>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label for="input-text"><?php _e('Event price :'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="_lt_event_price"  value="<?php echo $detal["_lt_event_price"] ?? ""; ?>" />
                    </td>
                    <th>
                        <label for="input-text"><?php _e('Event Currency :'); ?></label>
                    </th>
                    <td>
                            <?php echo $value = esc_attr( get_option( 'event_currency' ) );?>
                    </td>
                </tr>
                <tr>
                    <th>
                         <label for="input-text"><?php _e('Event Participe Limits:'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="_lt_event_partic_limit" value="<?php echo $detal["_lt_event_partic_limit"] ?? ""; ?>"/>
                    </td>
                    <th>
                        <label for="input-text"><?php _e('Event Country:'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="_lt_event_country" value="<?php echo $detal["_lt_event_country"] ?? ""; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                         <label for="input-text"><?php _e('Speakers:'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="_lt_event_peakers" value="<?php echo $detal["_lt_event_peakers"] ?? ""; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="input-text"><?php _e('Event City:'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="_lt_event_city" value="<?php echo $detal["_lt_event_city"] ?? ""; ?>"/>
                    </td>
                    <th>
                        <label for="input-text"><?php _e('Event Address:'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="_lt_event_address" value="<?php echo $detal["_lt_event_address"] ?? ""; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                         <label for="input-text"><?php _e('Event Street:'); ?></label>
                    </th>
                    <td>
                         <input type="text" name="_lt_event_street" value="<?php echo $detal["_lt_event_street"] ?? ""; ?>"/>
                    </td>
                    <th>
                        <label for="input-text"><?php _e('Event Email:'); ?></label>
                    </th>
                    <td>
                        <input type="email" name="_lt_event_email" value="<?php echo $detal["_lt_event_email"] ?? ""; ?>"/>
                    </td>
                    <tr>
                    <th>
                        <label for="input-text"><?php _e('Event Organizers email:'); ?></label>
                    </th>
                    <td>
                        <input type="email" name="_lt_event_organizer" value="<?php echo $detal["_lt_event_organizer"] ?? ""; ?>"/>
                    </td>
                    <th>
                        <label for="input-text"><?php _e('Event Phone:'); ?></label>
                    </th>
                    <td>
                        <input type="tel" name="_lt_event_phone" value="<?php echo $detal["_lt_event_phone"] ?? ""; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                    <label for="input-text"><?php _e('Event Phone 2:'); ?></label>
                    </th>
                    <td>
                    <input type="tel" name="_lt_event_phone_2" value="<?php echo $detal["_lt_event_phone_2"] ?? ""; ?>"/>
                    </td>
               
                </tr>               
            </tbody>
        </table>
    </form>
</div>

        <?php
    }

   // Save the Metabox Data
    public function lt_eventposts_save_meta($post_id, $post)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!isset($_POST['lt_eventposts_nonce']))
            return;
        if (!wp_verify_nonce($_POST['lt_eventposts_nonce'], plugin_basename(__FILE__)))
            return;
        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID))
            return;
        // OK, we're authenticated: we need to find and save the data
        // We'll put it into an array to make it easier to loop though

        $metabox_ids = array('_lt_start', '_lt_end');
        foreach ($metabox_ids as $key) {



            $aa =  sanitize_text_field( $_POST[$key . '_year']);
            $mm =  sanitize_text_field( $_POST[$key . '_month']);
            $jj =  sanitize_text_field( $_POST[$key . '_day']);
            $hh =  sanitize_text_field( $_POST[$key . '_hour']);
            $mn =  sanitize_text_field( $_POST[$key . '_minute']);

            $aa = ($aa <= 0) ? date('Y') : $aa;
            $mm = ($mm <= 0) ? date('n') : $mm;
            $jj = sprintf('%02d', $jj);
            $jj = ($jj > 31) ? 31 : $jj;
            $jj = ($jj <= 0) ? date('j') : $jj;
            $hh = sprintf('%02d', $hh);
            $hh = ($hh > 23) ? 23 : $hh;
            $mn = sprintf('%02d', $mn);
            $mn = ($mn > 59) ? 59 : $mn;

            $events_meta[$key . '_year'] = $aa;
            $events_meta[$key . '_month'] = $mm;
            $events_meta[$key . '_day'] = $jj;
            $events_meta[$key . '_hour'] = $hh;
            $events_meta[$key . '_minute'] = $mn;
            $events_meta[$key . '_eventtimestamp'] = $aa . '-' . $mm . '-' . $jj . ' ' . $hh . ':' . $mn;

        }

        // Save Locations Meta

        $events_meta['_lt_event_country'] = sanitize_text_field($_POST['_lt_event_country']);
        $events_meta['_lt_event_city'] = sanitize_text_field($_POST['_lt_event_city']);
        $events_meta['_lt_event_peakers'] = sanitize_text_field($_POST['_lt_event_peakers']);
        $events_meta['_lt_event_address'] = sanitize_text_field($_POST['_lt_event_address']);
        $events_meta['_lt_event_email'] = sanitize_email($_POST['_lt_event_email']);
        $events_meta['_lt_event_organizer'] = sanitize_text_field($_POST['_lt_event_organizer']);
        $events_meta['_lt_event_phone'] = $this->callbacks->lt_sanitize_number($_POST['_lt_event_phone']);
        $events_meta['_lt_event_phone_2'] = $this->callbacks->lt_sanitize_number($_POST['_lt_event_phone_2']);
        $events_meta['_lt_event_street'] = sanitize_text_field($_POST['_lt_event_street']);
        $events_meta['_lt_event_partic_limit'] = $this->callbacks->lt_sanitize_number($_POST['_lt_event_partic_limit']);
        $events_meta['_lt_event_price'] = $this->callbacks->lt_sanitize_number($_POST['_lt_event_price']);

            if ($post->post_type == 'revision') return; // Don't store custom data twice

            $detal = get_post_meta($post->ID, '_event_detall_info', false); 
            if( $detal ) {
                update_post_meta($post->ID, '_event_detall_info', $events_meta);

            }else{
                add_post_meta($post->ID, '_event_detall_info', $events_meta);
 
            }

        // Add values of $events_meta as custom fields
        // foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
        //     if ($post->post_type == 'revision') return; // Don't store custom data twice
        //     $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        //     if (get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
        //         update_post_meta($post->ID, $key, $value);
        //     } else { // If the custom field doesn't have a value
        //         add_post_meta($post->ID, $key, $value);
        //     }
        //     if (!$value) delete_post_meta($post->ID, $key); // Delete if blank
        // }
    }
    /**
     * Helpers to display the date on the front end
     */
    // Get the Month Abbreviation

    public function lt_get_the_month_abbr($month)
    {
        global $wp_locale;
        for ($i = 1; $i < 13; $i = $i + 1) {
            if ($i == $month)
                $monthabbr = $wp_locale->get_month_abbrev($wp_locale->get_month($i));
        }
        return $monthabbr;
    }

    // Display the date

    public function lt_get_the_event_date()
    {
        global $post;

        $eventdate = '';
        $month = get_post_meta($post->ID, '_month', true);
        $eventdate = lt_get_the_month_abbr($month);
        $eventdate .= ' ' . get_post_meta($post->ID, '_day', true) . ',';
        $eventdate .= ' ' . get_post_meta($post->ID, '_year', true);
        $eventdate .= ' at ' . get_post_meta($post->ID, '_hour', true);
        $eventdate .= ':' . get_post_meta($post->ID, '_minute', true);
        return $eventdate;
    }

    /**
     * Customize Event Query using Post Meta
     *
     * @link http://www.billerickson.net/customize-the-wordpress-query/
     * @param object $query data
     *
     */
    public function lt_event_query($query)
    {

        // http://codex.wordpress.org/Function_Reference/current_time
        $current_time = current_time('mysql');
        list($today_year, $today_month, $today_day, $hour, $minute, $second) = preg_split('([^0-9])', $current_time);
        $current_timestamp = $today_year . $today_month . $today_day . $hour . $minute;
        global $wp_the_query;

        if ($wp_the_query === $query && !is_admin() && is_post_type_archive('events')) {
            $meta_query = array(
                array(
                    'key' => '_lt_start_eventtimestamp',
                    'value' => $current_timestamp,
                    'compare' => '>'
                )
            );
            $query->set('meta_query', $meta_query);
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', '_lt_start_eventtimestamp');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', '2');
        }

    }

}

new EventMetaBox();