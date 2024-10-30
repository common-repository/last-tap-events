
    <?php
get_header( );
     function lt_prepend_event_meta_to_content($content = null)
    {
        global $post, $wp_locale;
        $plugin_url = plugin_dir_url(dirname(__FILE__, 1));
        $event_post = $post;
        
            // Gets the event start month from the meta field
            $month = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_start_month'];
            // Converts the month number to the month name
            $month = $wp_locale->get_month_abbrev($wp_locale->get_month($month));
            // Gets the event start day
            $day = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_start_day'];
            // Gets the event start year
            $year = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_start_year'];
            $event_partici = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_partic_limit'];
            $event_peakers = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_peakers'];
            $start = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_start_eventtimestamp'];
            $endEvent = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_end_eventtimestamp'];
            $date_end = new \DateTime($endEvent); // DATE END
            $end_new = (date_i18n($date_end->format('Y/m/d H:i')));
            $date = new \DateTime($start); // DATE STARTE
            $data_new = (date_i18n($date->format('m/d')));
            $date_final = (date_i18n($date->format('Y/m/d H:i')));

            $count_participant = ( new LastTap_EventController())->lastTap_count_event_participant($event_post->ID);    
            $price = get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_price'];
            $number = 0;

                if($event_partici != 0){
                    $number = ($event_partici - count($count_participant));
                }else{
                    $number = 0;
                }

                $currency = get_option( 'event_currency', true );

                if("" == $currency ){
                    $currency = 'USD';
                }

                if( empty($price) ){
                    $price = __('Free', 'last-tap-events');
                    $currency = null;
                }
            ?>
        <header class="event-header-post">
            <?php echo get_the_post_thumbnail($event_post->ID, 'event-thumb'); ?>
            <div class="lastTap overlay"></div>            
            <div class="lastTap container h-100">
                <div class="lastTap d-flex text-center h-100">
                    <div class="lastTap my-auto w-100 p-4">
                        <div class="from-to">
                        <h4> <?php  printf( __('From %s To %s', 'last-tap-events'), $data_new, $end_new); ?></h4>
                        </div>
                        <h1 class="lastTap display-3 pt-4"><?php the_title();?></h1>
                        <div class="countdown-header" style="color:<?php echo  esc_attr( get_option( 'event_text_header_color' ));?>">

                            <div class="countdown" data-date="<?php echo esc_html($date_final); ?>"></div>                            
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="lastTap col-lg-12 ">
            <div class="lastTap container">
                <div class="lastTap col-lg-12 text-center">
                    <div class="lastTap  row pt-4">
                        <div class="lastTap  col-md-3 col-sm-6 col-xs-12">
                            <h4><?php echo $event_partici; ?></h4>
                            <p><?php _e('Attendees','last-tap-event');?></p>
                        </div>
                        <div class="  col-md-3 col-sm-6 col-xs-12">
                            <h4><?php echo $number;?></h4>
                            <p><?php _e('Places available', 'last-tap-events'); ?></p>
                        </div>
                        <div class="  col-md-3 col-sm-6 col-xs-12">
                            <h4><?php echo $event_peakers; ?></h4>
                            <p>SPEAKERS</p>
                        </div>
<!--                         <div class=" col-md-3 col-sm-6 col-xs-12">
                            <h4>2000+</h4>
                            <p>Attendees</p>
                        </div>
 -->                    </div>
                </div>
            </div>
        </div>

            <div class="lastTap col-lg-12 m-4 text-center h-100">
                <div class="lastTap container">
                    <div class="lastTap col-lg-12">
                        <div class="lastTap  row pt-4">
                            <div class="lastTap col-md-3 col-sm-6 col-xs-12">
                            <img src="<?php echo $plugin_url;?>assets/icon/location.svg" style="width:30px; height:40px;">
                            <br>
                                                <?php _e(' Location', 'last-tap-events'); ?>
                            <address><?php echo "\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_country'] . ",\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_city']; ?></address>
                            <address title="<?php echo "\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_address'] .", \n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_street']; ?>"><?php echo "\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_address'] .", \n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_street']; ?></address>

                            </div>
                            <div class="lastTap col-md-3 col-sm-6 col-xs-12">
                                        
                                <img src="<?php echo $plugin_url . "/assets/icon/clock.svg"; ?>" style="width:40px; height:40px;">
                                <br>
                                                <?php echo "\t\n" . __('Start Event:', 'last-tap-events'); ?><br><br>
                                <time><?php echo "\t\n" . (new LastTap_EventCallbacks())->formatDate($start); ?></time>
            
                            </div>
                            <div class="lastTap col-md-3 col-sm-6 col-xs-12">
                                <h4><?php the_title();?></h4>
                            </div>
                            <!-- <div class="lastTap col-md-3 col-sm-6 col-xs-12">
                                <h4><?php the_title();?></h4>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        <div class="lastTap col-lg-12 m-4 h-100">
            <div class="lastTap"></div>
            <div class="lastTap container">
                <div class="lastTap col-lg-12">
                    <div class="lastTap  row">
                        <div class="lastTap  col-sm-6">
                            <div id="map"></div>
                                <script>
                                    var map;
                                    function initMap() {
                                    map = new google.maps.Map(document.getElementById('map'), {
                                    center: {lat: -8.83833, lng: 13.2344},
                                            zoom: 8
                                        });
                                    }
                                </script>
                                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlLLDyuucPNNDst12Ahe_82hBaD9GBLWY&callback=initMap"  async defer></script>
                        </div>
                        <div class="lastTap  col-sm-6">
                            <h4><?php the_title();?></h4>

                            <label class="lastTap center">
                                <h2><?php _e(' Contact', 'last-tap-events'); ?></h2></label>
                            <br>
                            <?php _e('Event Phone:', 'last-tap-events'); ?>
                            <?php echo "\t\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_phone']; ?>
                            <br>
                            <?php _e('Event Phone 2:', 'last-tap-events'); ?>
                            <?php echo "\t\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_phone_2']; ?>
                            <br>
                            <hr>
                            <?php _e('Event Email:', 'last-tap-events'); ?>
                            <?php echo "\t\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_email']; ?>
                            <br>
                            <?php _e('Event or organizer email:', 'last-tap-events'); ?></strong>
                            <?php echo "\t\n" . get_post_meta($event_post->ID, '_event_detall_info', true)['_lt_event_organizer']; ?>
                            <br>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="lastTap col-lg-12 m-5">
            <div class="container">
                <header class="event-about text-center">
                    <h2><?php _e('About Event','last-tap-events');?></h2>
                </header>
                    <p><?php echo $event_post->post_content;?></p>
            </div>                        
        </div>
        <div class="lastTap col-lg-12">
            <div class="lastTap row">
                <hr>
                <div class="lastTap col-lg-12">
                    <?php 
                    if($event_partici == count($count_participant)){?>
                        <header class="lastTap header">
                            <button class="lastTap tab" style="background: red;" onclick="myFunction()"><?php esc_html_e( 'INSCRIPTIONS ARE CLOSED! We have reached the maximum number of members, and that is why registration is closed.!', 'last-tap-events' );?></button>
                        </header>
                    <?php }else{
                        ( new LastTap_EventController())->get_participe_event_form();
                    } ?>

                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 wow fadeInLeft" data-wow-delay="0.2s" style="visibility: visible;">
                </div>
            </div>
        </div>



<?php 
    }


echo lt_prepend_event_meta_to_content();
        get_footer( );
