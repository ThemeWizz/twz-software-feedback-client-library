# twz-software-feedback

This library will show a de-activation survey dialog when the user clicks "deactivate" on plugins.php on any plugins you've bound the library to. The form data will be posted to ccplugins.co

## Installation
Use composer to manage your dependencies and download PHP-JWT:

```bash
composer require themewizz/twz-software-feedback-client-library
```

## Usage

	add_filter('twz_software_deactivate_feedback_plugins', function($plugins) {
	
		$plugins[] = (object)array(
			'slug' => 'wp-google-maps',
			'server_url' => '/wp-admin/admin-ajax.php',
			'version' => '7.0.6'
		);
	
		return $plugins;
	
	});

	add_filter('twz_software_deactivate_feedback_plugins', array($this, 'twz_software_deactivate_feedback_form_plugins'));	
	public function twz_software_deactivate_feedback_form_plugins($plugins)
	{
		$plugins[] = (object)array(
			'server_url' => '/wp-admin/admin-ajax.php',
			'slug' => 'wp-google-maps',
			'version' => '5.5.1,
			'url' => 'https://themewizz.com',
			'lang' => get_bloginfo('language'),
			'email' => get_bloginfo('admin_email')
		);

		return $plugins;
	}

	Default Strings
	add_filter('twz_software_deactivate_feedback_form_default_strings', array($this 'twz_software_deactivate_feedback_form_default_strings'), 10, 2);
	public function twz_software_deactivate_feedback_form_default_strings($defaultStrings, $plugin)
	{
		if ($plugin->slug == "wp-google-maps") {
			$defaultStrings = array(
				'quick_feedback'			=> __('Quick Feedback', 'codecabin'),
				'foreword'					=> __('If you would be kind enough, please tell us why you\'re deactivating?', 'codecabin'),
				'better_plugins_name'		=> __('Please tell us which plugin?', 'codecabin'),
				'please_tell_us'			=> __('Please tell us the reason so we can improve the plugin', 'codecabin'),
				'do_not_attach_email'		=> __('Do not send my e-mail address with this feedback', 'codecabin'),

				'brief_description'			=> __('Please give us any feedback that could help us improve', 'codecabin'),

				'cancel'					=> __('Cancel', 'codecabin'),
				'skip_and_deactivate'		=> __('Skip & Deactivate', 'codecabin'),
				'submit_and_deactivate'		=> __('Submit & Deactivate', 'codecabin'),
				'please_wait'				=> __('Please wait', 'codecabin'),
				'thank_you'					=> __('Thank you!', 'codecabin')
			);
		}
		return $defaultStrings;
	}

	Reasons
	add_filter('twz_software_deactivate_feedback_form_reasons', array($this, 'twz_software_deactivate_feedback_form_reasons'), 10, 2);
	public function twz_software_deactivate_feedback_form_reasons($defaultReasons, $plugin)
	{
		if ($plugin->slug == "wp-google-maps") {
			$defaultReasons = array(
				'suddenly-stopped-working'	=> __('The plugin suddenly stopped working', 'codecabin'),
				'plugin-broke-site'			=> __('The plugin broke my site', 'codecabin'),
				'no-longer-needed'			=> __('I don\'t need this plugin any more', 'codecabin'),
				'found-better-plugin'		=> __('I found a better plugin', 'codecabin'),
				'temporary-deactivation'	=> __('It\'s a temporary deactivation, I\'m troubleshooting', 'codecabin'),
				'other'						=> __('Other', 'codecabin')
			);
		}
		return $defaultReasons;
	}




## Notes

You can include any other arbitrary data you might want to store in the object you pass to the $plugins array in the example above
