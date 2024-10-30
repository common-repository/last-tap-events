<?php
/**
 * @version 1.0
 *
 * @package LastTapEvents/inc/controller
 * @see LastTap_BaseController
 */

defined('ABSPATH') || exit;


class LastTap_TemplateController extends LastTap_BaseController
{
    public $templates;

    public function lt_register()
    {
        // if (!$this->lt_activated('templates_manager')) return;

        $this->templates = array(
            'page-templates/location-template.php' => __('Location  Layout', 'last-tap-events'),
            'page-templates/event-template.php' => __('Event  Layout', 'last-tap-events'),
            'page-templates/event-membership.php' => __('Event MemberShip Layout', 'last-tap-events'),
            'page-templates/homeEventTemplate.php' => __('HomeEventTemplate', 'last-tap-events'),
        );

        add_filter('theme_page_templates', array($this, 'lt_custom_template'));
        add_filter('template_include', array($this, 'lt_load_template'));
        add_filter('single_template', array($this, 'load_movie_template'));

    }

    public function lt_custom_template($templates)
    {
        $templates = array_merge($templates, $this->templates);

        return $templates;
    }

    public function lt_load_template($template)
    {
        global $post;

        if (!$post) {
            return $template;
        }

        // If is the front page, load a custom template
        if (is_front_page()) {
            $file = $this->plugin_path . 'page-templates/front-page.php';

            if (file_exists($file)) {
                return $file;
            }
        }

        $template_name = get_post_meta($post->ID, '_wp_page_template', true);

        if (!isset($this->templates[$template_name])) {
            return $template;
        }

        $file = $this->plugin_path . $template_name;

        if (file_exists($file)) {
            return $file;
        }

        return $template;
    }


    function load_movie_template( $template ) {
    global $post;

    if ( 'event' === $post->post_type && locate_template( array( 'single-event.php' ) ) !== $template ) {
        /*
         * This is a 'movie' post
         * AND a 'single movie template' is not found on
         * theme or child theme directories, so load it
         * from our plugin directory.
         */
            $file = $this->plugin_path . 'page-templates/single-event.php';

            if (file_exists($file)) {
                return $file;
            }    
        }

    return $template;
    }
}