<?php
/**
 * @version 1.0
 *
 * @package LastTapEvents/inc/api/callbacks
 */

defined('ABSPATH') || exit;


class LastTap_CalendarCallbacks extends LastTap_BaseController
{
    public function lt_calendarPage()
    {
        return require_once("$this->plugin_path/templates/calendar.php");
    }

    public function lt_calendarSectionManager(){
    	echo "string";
    }

}
