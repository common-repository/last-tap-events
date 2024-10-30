<?php

/**
 * @version 1.0
 *
 * @package LastTapEvents/inc/controller
 * @see LastTap_Calendar
 */

defined('ABSPATH') || exit;


class LastTap_Calendar extends LastTap_BaseController
{

    public $settings;

    public $callbacks;

    public $calendar_callbacks;

    public $subpages = array();
    public function lt_register(){
	
		if (!$this->lt_activated('calendar_manager')) return;

        $this->settings = new LastTap_SettingsApi();


        $this->calendar_callbacks = new LastTap_CalendarCallbacks();
        $this->lt_setSubpages();

        $this->lt_setSettings();

        $this->lt_setSections();

        $this->lt_setFields();

        $this->settings->lt_addSubPages($this->subpages)->lt_register();

    }
    public function lt_setSubpages()
    {
        $subpage = array(
            array(
                'parent_slug' => 'event_plugin_page',
                'page_title' => 'Calendar',
                'menu_title' => 'Calendar',
                'capability' => 'manage_options',
                'menu_slug' => 'event_calendar',
                'callback' => array($this->calendar_callbacks, 'lt_calendarPage')
            )
        );

        $this->settings->lt_addSubPages($subpage)->lt_register();
    }

    public function lt_setFields(){

    }
    public function lt_setSettings()
    {
			$args = array(
            array(
                'option_group' => 'event_plugin_calendar_settings',
                'option_name' => 'event_plugin_calendar',
                'callback' => array($this->calendar_callbacks, 'lt_calendarSanitize')
            )
        );

        $this->settings->lt_setSettings($args);
    
    }
    public function lt_setSections()
    {
		$args = array(
		            array(
		                'id' => 'event_calendar_index',
		                'title' => __('Calendar Manager', 'last-tap-events'),
		                'callback' => array($this->calendar_callbacks, 'lt_calendarSectionManager'),
		                'page' => 'event_calendar'
		            )
		        );

		        $this->settings->lt_setSections($args);
		   }
}
 