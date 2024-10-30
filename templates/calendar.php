<div class="wrap">
                        <h3><?php _e( 'Calendar', 'last-tap-events' ); ?></h3>
                        <?php $the_posts = get_posts(array('post_type' => 'event'));
                            $data = array();
                        foreach ($the_posts as $key => $value) {
                            $post_id = $value->ID;
                        
                        $title['title'] = $value->post_title;
                        $_event_detall_info = get_post_meta( $post_id, '_event_detall_info',  true );
                        $start['start'] = $_event_detall_info['_lt_start_eventtimestamp']; 
                        $end['end'] = $_event_detall_info['_lt_end_eventtimestamp'];
                        $data[] = array_merge($title, $start, $end);
                        }
                        $my = json_encode($data);
                        $locale = substr( get_locale(), 0, -3);

                        ?>
                              <div id='calendar'></div>

                            <script>

                                var today = new Date();
                                var dd = String(today.getDate()).padStart(2, '0');
                                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                                var yyyy = today.getFullYear();

                                today = yyyy + '-' + mm + '-' + dd;

                                document.addEventListener('DOMContentLoaded', function() {
                                    
                                    var initialLocaleCode ="<?php  echo $locale;?>";
                                    

                                var calendarEl = document.getElementById('calendar');

                                var calendar = new FullCalendar.Calendar(calendarEl, {
                                  plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                                  header: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                                  },
                                  locale: initialLocaleCode,
                                  defaultDate: today,
                                  navLinks: true, // can click day/week names to navigate views
                                  businessHours: true, // display business hours
                                  editable: true,
                                  selectable: true,
                                  selectMirror: true,
                                  select: function(arg) {
                                    // var title = prompt('Event Title:');
                                    // if(title) {
                                    //     calendar.addEvent({
                                    //         title: title,
                                    //         start: arg.start,
                                    //         end: arg.end,
                                    //         allDay: arg.allDay
                                    //     })
                                    // }
                                    calendar.unselect()
                                  },
                                  events: <?php echo $my; ?>
                                });

                                calendar.render();
                              });

                            </script>
                            <style>

                              #calendar {
                                max-width: 900px;
                                margin: 0 auto;
                              }

                            </style>

</div>

