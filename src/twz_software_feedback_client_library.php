<?php

namespace sersart\twz_software_feedback_client_library;

if (!is_admin())
	return;

global $pagenow;

if ($pagenow != "plugins.php")
	return;

if (defined('TWZ_SOFTWARE_DEACTIVATE_FEEDBACK_FORM_INCLUDED'))
	return;
define('TWZ_SOFTWARE_DEACTIVATE_FEEDBACK_FORM_INCLUDED', true);

add_action('admin_enqueue_scripts', function () {

	// Enqueue scripts
	wp_enqueue_script('remodal', plugin_dir_url(__FILE__) . 'assets/js/remodal.min.js');
	wp_enqueue_style('remodal', plugin_dir_url(__FILE__) . 'assets/css/remodal.css');
	wp_enqueue_style('remodal-default-theme', plugin_dir_url(__FILE__) . 'assets/css/remodal-default-theme.css');

	wp_enqueue_script('twz-software-feedback-form', plugin_dir_url(__FILE__) . 'assets/js/twz-software-feedback-form.js');
	wp_enqueue_style('twz-software-feedback-form', plugin_dir_url(__FILE__) . 'assets/css/twz-software-feedback-form.css');

	// Plugins
	$plugins = apply_filters('twz_software_deactivate_feedback_plugins', array());

	// Reasons
	foreach ($plugins as $plugin) {
		$plugin->strings = apply_filters('twz_software_deactivate_feedback_form_default_strings', array(), $plugin);
		$plugin->reasons = apply_filters('twz_software_deactivate_feedback_form_reasons',  array(), $plugin);
	}

	// Localized strings
	wp_localize_script('twz-software-feedback-form', 'twz_software_deactivate_feedback_plugins', $plugins);
});

/**
 * Hook for adding plugins, pass an array of objects in the following format:
 *  'slug'		=> 'plugin-slug'
 *  'version'	=> 'plugin-version'
 * @return array The plugins in the format described above
 */
add_filter('twz_software_deactivate_feedback_plugins', function ($plugins) {
	return $plugins;
});
