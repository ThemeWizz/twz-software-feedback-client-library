<?php

namespace themewizz\feedback;

use function WPML\FP\Strings\replace;

abstract class TWZ_Software_Feedback_Client
{
	protected $server_url = '';
	protected $plugin_slug = '';
	protected $plugin_version = '';

	public function __construct()
	{
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_action('admin_footer', array($this, 'deactivate_scripts'));
	}
	protected abstract function get_uninstall_reasons();
	protected abstract function get_default_strings();

	public function admin_enqueue_scripts()
	{
		// Enqueue scripts
		wp_enqueue_script($this->plugin_slug . '-remodal', plugin_dir_url(__FILE__) . 'assets/js/remodal.js');
		wp_enqueue_style($this->plugin_slug . '-remodal', plugin_dir_url(__FILE__) . 'assets/css/remodal.css');
		wp_enqueue_style($this->plugin_slug . '-remodal-default-theme', plugin_dir_url(__FILE__) . 'assets/css/remodal-default-theme.css');
	}
	public function getHost()
	{
		if (!empty($_SERVER['HTTPS'])) {
			return 'https://' . $_SERVER['HTTP_HOST'];
		} else {
			return 'http://' . $_SERVER['HTTP_HOST'];
		}
	}
	public function deactivate_scripts()
	{
		global $pagenow;
		if ('plugins.php' != $pagenow) {
			return;
		}

		$app_id = str_replace('-', '_', $this->plugin_slug);
		$reasons = $this->get_uninstall_reasons();
		$default_strings = $this->get_default_strings();

?>

		<div id='<?php echo $this->plugin_slug; ?>-software-deactivate-dialog' class='twz-software-deactivate-dialog' data-remodal-id='<?php echo $this->plugin_slug; ?>'>
			<form>
				<input type='hidden' name='action' value='twz_software_deactivate_feedback'>
				<input type='hidden' name='title' value='Deactivation'>
				<input type='hidden' name='twz_plugin_slug' value='<?php echo $this->plugin_slug; ?>'>
				<input type='hidden' name='twz_plugin_version' value='<?php echo $this->plugin_version; ?>'>
				<input type='hidden' name='twz_plugin_home_url' value='<?php echo home_url(); ?>'>
				<input type='hidden' name='twz_plugin_site_url' value='<?php echo site_url(); ?>'>
				<input type='hidden' name='twz_plugin_locale' value='<?php echo get_locale(); ?>'>
				<input type='hidden' name='twz_plugin_email' value='<?php echo get_bloginfo( 'admin_email' ); ?>'>
				<div class='twz-software-deactivate-dialog-header'>
					<div>
						<span class='twz-software-deactivate-dialog-header-title'><?php echo $default_strings['title']; ?></span>
					</div>
				</div>
				<div class='dialog-message'>
					<div class='twz-software-deactivate-dialog-form-caption'><?php echo $default_strings['foreword']; ?></div>
					<ul class='twz-software-deactivate-reasons'>
						<?php foreach ($reasons as $reason) { ?>
							<li data-type="<?php echo esc_attr($reason['type']); ?>" data-placeholder="<?php echo esc_attr($reason['placeholder']); ?>">
								<label>
									<input type="radio" name="twz_plugin_reason" value="<?php echo esc_attr($reason['id']); ?>">
									<?php echo esc_html($reason['text']); ?>
								</label>
							</li>
						<?php } ?>
					</ul>
				</div>
				<div class='twz-software-deactivate-dialog-buttons'>
					<input type='submit' class='button confirm' value='<?php echo $default_strings['skip_and_deactivate']; ?>'>
					<button data-remodal-action='cancel' class='button button-primary'><?php echo $default_strings['cancel']; ?></button>
				</div>
			</form>
		</div>

		<style type="text/css">
			.twz-software-deactivate-dialog {
				text-align: left;
				width: 600px !important;
				padding: 0px !important;
			}

			.twz-software-deactivate-dialog-header {
				padding: 18px 15px;
				box-shadow: 0 0 8px rgba(0, 0, 0, .1);
				text-align: start;
				background-color: #e2e2e2;
			}

			.twz-digital-download-header-title-logo {
				width: 32px;
				height: 32px;
			}

			.twz-software-deactivate-dialog-header-title {
				font-size: 15px;
				text-transform: uppercase;
				font-weight: 700;
				padding-inline-start: 5px;
			}

			.twz-software-deactivate-dialog .dialog-message {
				padding: 20px;
				padding-block-end: 0;
				text-align: start;
			}

			.twz-software-deactivate-dialog-form-caption {
				font-weight: 700;
				font-size: 15px;
				line-height: 1.4;
				margin-bottom: 10px;
			}

			.twz-software-deactivate-dialog-buttons {
				display: flex;
				justify-content: space-between;
				padding: 20px 30px;
				background-color: #e2e2e2;
			}

			.twz-software-deactivate-dialog input[name="comments"] {
				width: 415px;
				margin-top: 10px;
			}

			.twz-software-deactivate-dialog .twz-deactivate-feedback-dialog-input:not(:checked)~.twz-feedback-text {
				display: none;
			}
		</style>

		<script type="text/javascript">
			(function($) {
				if (!window.<?php echo $app_id; ?>)
					window.<?php echo $app_id; ?> = {};


				<?php echo $app_id; ?>.TwzDeactivateFeedbackForm = function() {
					var self = this;

					// Dialog HTML element
					var element = $('#<?php echo $this->plugin_slug; ?>-software-deactivate-dialog');

					this.element = element;

					$(element).on("click", "input[name='twz_plugin_reason']", function(event) {
						$(element).find("input[type='submit']").val('<?php echo $default_strings['submit_and_deactivate']; ?>');
						$(element).find('.reason-input').remove();
						var parent = $(this).parents('li:first');
						var inputType = parent.data('type'),
							inputPlaceholder = parent.data('placeholder'),
							reasonInputHtml = '<div class="reason-input">' + (('text' === inputType) ? '<input name="twz_plugin_comments" type="text" class="input-text" size="40" />' : '<textarea name="twz_plugin_comments" rows="5" cols="45"></textarea>') + '</div>';

						if (inputType !== '') {
							parent.append($(reasonInputHtml));
							parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
						}
					});

					$(element).find("form").on("submit", function(event) {
						self.onSubmit(event);
					});

					// Listen for deactivate
					$("#the-list [data-slug='<?php echo $this->plugin_slug; ?>'] .deactivate>a").on("click", function(event) {
						self.onDeactivateClicked(event);
					});
				}

				<?php echo $app_id; ?>.TwzDeactivateFeedbackForm.prototype.onDeactivateClicked = function(event) {
					this.deactivateURL = event.target.href;

					event.preventDefault();

					//if (!this.dialog)
					this.dialog = $('#<?php echo $this->plugin_slug; ?>-software-deactivate-dialog').remodal();
					this.dialog.open();
				}

				<?php echo $app_id; ?>.TwzDeactivateFeedbackForm.prototype.onSubmit = function(event) {
					var element = this.element;
					var self = this;
					var data = $(element).find("form").serialize();

					$(element).find("button, input[type='submit']").prop("disabled", true);

					if ($(element).find("input[name='twz_plugin_reason']:checked").length) {
						$(element).find("input[type='submit']").val('<?php echo $default_strings['thank_you']; ?>');

						$.ajax({
							type: "POST",
							url: '<?php echo $this->server_url; ?>/wp-admin/admin-ajax.php',
							data: data,
							complete: function() {
								window.location.href = self.deactivateURL;
							}
						});
					} else {
						$(element).find("input[type='submit']").val('<?php echo $default_strings['please_wait']; ?>');
						window.location.href = self.deactivateURL;
					}

					event.preventDefault();
					return false;
				}

				$(document).ready(function() {
					new <?php echo $app_id; ?>.TwzDeactivateFeedbackForm();
				});

			})(jQuery);
		</script>

<?php
	}
}
