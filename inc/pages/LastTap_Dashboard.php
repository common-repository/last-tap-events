<?php
/**
 * @version 1.0
 *
 * @package LastTapEvents/inc/controller
 * @see LastTap_BaseController
 */

defined('ABSPATH') || exit;


class LastTap_Dashboard extends LastTap_BaseController
{
    public $settings;

    public $admin_callbacks;

    public $callbacks_mngr;

    public $pages = array();

    public function lt_register()
    {
        $this->settings = new LastTap_SettingsApi();

        $this->admin_callbacks = new LastTap_AdminCallbacks();

        $this->callbacks_mngr = new LastTap_ManagerCallbacks();


        $this->lt_setPages();

        $this->lt_setSettings();
        $this->lt_setSections();
        $this->lt_setFields();

        $this->settings->lt_addPages($this->pages)->lt_withSubPage('Dashboard')->lt_register();

    }

    public function lt_setPages()
    {
        $this->pages = array(
            array(
                'page_title' => 'Last Tap Events',
                'menu_title' => 'Last Tap Events',
                'capability' => 'manage_options',
                'menu_slug' => 'event_plugin_page',
                'callback' => array($this->admin_callbacks, 'lt_adminDashboard'),
                'icon_url' => 'dashicons-store',
                'position' => 110
            )
        );
    }

    public function lt_setSettings()
    {
        $args = array(
            array(
                'option_group' => 'event_plugin_settings',
                'option_name' => 'event_plugin',
                'callback' => array($this->callbacks_mngr, 'lt_checkboxSanitize')
           ),
            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_status_started'
            ),
            array(


                'option_group' => 'event_options_group',
                'option_name' => 'event_status_finished'
            ),
            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_status_soon'
            ),
            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_status_button'
            ),

            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_text_header_color',
                'callback' => array($this->admin_callbacks, 'lt_event_header_sanitize_color')

            ),
            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_border_color',
                'callback' => array($this->admin_callbacks, 'lt_event_sanitize_color')

            ),
            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_currency',
                'callback' => array($this->admin_callbacks, 'lt_validate_currency')

            ),
            array(
                'option_group' => 'event_options_group',
                'option_name' => 'event_background_color_button_show_form',
                'callback' => array($this->admin_callbacks, 'lt_event_sanitize_background_color')
            ),
            array('option_group' => 'event_options_group',
                'option_name' => 'event_text_color_button_show_form',
                'callback' => array($this->admin_callbacks, 'lt_event_sanitize_text_color')

        ),
            array('option_group' => 'event_options_group',
                'option_name' => 'event_google_key_map',
                'callback' => array($this->admin_callbacks, 'lt_event_sanitize_google_key_map')

        )
        );

        $this->settings->lt_setSettings($args);
    }

    public function lt_setSections()
    {
        $args = array(
            array(
                'id' => 'event_admin_section',
                'title' => 'Settings Manager',
                'callback' => array($this->callbacks_mngr, 'lt_adminSectionManager'),
                'page' => 'event_plugin'
            ),

            array(
                'id' => 'event_id',
                'title' => __( 'Settings', 'last-tap-events'),
                'callback' => array($this->admin_callbacks, 'lt_event_section'),
                'page' => 'event_settings'

            ),
            array(
                'id' => 'event_color_section',
                'title' => __( 'Color Control', 'last-tap-events'),
                'callback' => array($this->admin_callbacks, 'lt_event_section_color'),
                'page' => 'colors'

            )
        );


        $this->settings->lt_setSections($args);
    }

    public function lt_setFields()
    {
        $args = array();

      $args = array(

            array(
                'id' => 'event_currency_id',
                'title' => __('Currency ', 'last-tap-events'),
                'callback' => array($this->admin_callbacks, 'lt_currency'),
                'page' => 'event_settings',
                'section' => 'event_id',
                'args' => array(
                    'laber_for' => 'event_currency',
                    'class' => 'example-class'
                ),

            ),
            array(
                'id'=> 'event_text_header_color_id',
                 'title' => __('Color Header Text', 'last-tap-events'),
                 'callback' => array($this->admin_callbacks, 'lt_chanche_header_text_color'),
                 'page' => 'colors',
                 'section'=> 'event_color_section',
                 'args' => array(
                    'label' => 'event_text_header_color',
                    'class' => 'exemple-class'
                 ),
            ),

            array(
                'id' => 'event_border_color_id',
                'title' => __('Border color', 'last-tap-events'),
                'callback' => array($this->admin_callbacks, 'lt_event_textFields_border'),
                'page' => 'colors',
                'section' => 'event_color_section',
                'args' => array(
                    'laber_for' => 'event_border_color',
                    'class' => 'example-class'
                ),

            ),
            array(
                'id'=> 'event_background_color_button_show_form_id',
                 'title' => __('Color background button', 'last-tap-events'),
                 'callback' => array($this->admin_callbacks, 'lt_chanche_background_color_button'),
                 'page' => 'colors',
                 'section'=> 'event_color_section',
                 'args' => array(
                    'label' => 'event_background_color_button_show_form',
                    'class' => 'exemple-class'
                 ),
            ),

            array(
                'id'=> 'event_text_color_button_show_form_id',
                 'title' => __('Text Color button', 'last-tap-events'),
                 'callback' => array($this->admin_callbacks, 'lt_chanche_text_color_button'),
                 'page' => 'colors',
                 'section'=> 'event_color_section',
                 'args' => array(
                    'label' => 'event_text_color_button_show_form',
                    'class' => 'exemple-class'
                 ),
            ),

            array(
                'id'=> 'event_google_key_map',
                 'title' => __('Google Key Map', 'last-tap-events'),
                 'callback' => array($this->admin_callbacks, 'lt_google_map_key'),
                 'page' => 'event_settings',
                 'section'=> 'event_id',
                 'args' => array(
                    'option_name' => 'event_google_key_map',
                    'class' => 'exemple-class'
                 ),
            ),

        );
        foreach ($this->managers as $key => $value) {
            $args[] = array(
                'id' => $key,
                'title' => $value,
                'callback' => array($this->callbacks_mngr, 'lt_checkboxField'),
                'page' => 'event_plugin',
                'section' => 'event_admin_section',
                'args' => array(
                    'option_name' => 'event_plugin',
                    'label_for' => $key,
                    'class' => 'ui-toggle'
                )
            );            

        }

        $this->settings->lt_setFields($args);
    }

}