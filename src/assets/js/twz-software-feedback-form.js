(function ($) {

	if (!window.twz)
		window.twz = {};

	if (twz.DeactivateFeedbackForm)
		return;

	twz.DeactivateFeedbackForm = function (plugin) {
		var self = this;
		var strings = plugin.strings;

		this.plugin = plugin;

		// Dialog HTML
		var element = $('\
			<div class="twz-software-deactivate-dialog" data-remodal-id="' + plugin.slug + '">\
				<form>\
					<input type="hidden" name="action" value="twz_software_deactivate_feedback" />\
					<input type="hidden" name="title" value="Deactivation" />\
					<input type="hidden" name="slug" value="' + plugin.slug + '"/>\
					<input type="hidden" name="version" value="' + plugin.version + '"/>\
					<input type="hidden" name="url" value="' + plugin.url + '"/>\
					<input type="hidden" name="lang" value="' + plugin.lang + '"/>\
					<input type="hidden" name="email" value="' + plugin.email + '"/>\
					<h2>' + strings.quick_feedback + '</h2>\
					<p>\
						' + strings.foreword + '\
					</p>\
					<ul class="twz-software-deactivate-reasons"></ul>\
					<input name="comments" placeholder="' + strings.brief_description + '"/>\
					<br>\
					<p class="twz-software-deactivate-dialog-buttons">\
						<input type="submit" class="button confirm" value="' + strings.skip_and_deactivate + '"/>\
						<button data-remodal-action="cancel" class="button button-primary">' + strings.cancel + '</button>\
					</p>\
				</form>\
			</div>\
		')[0];

		this.element = element;

		$(element).find("input[name='plugin']").val(JSON.stringify(plugin));

		$(element).on("click", "input[name='reason']", function (event) {
			$(element).find("input[type='submit']").val(
				strings.submit_and_deactivate
			);
		});

		$(element).find("form").on("submit", function (event) {
			self.onSubmit(event, plugin);
		});

		// Reasons list
		var ul = $(element).find("ul.twz-software-deactivate-reasons");
		for (var key in plugin.reasons) {
			var li = $("<li><input type='radio' name='reason'/> <span></span></li>");

			$(li).find("input").val(key);
			$(li).find("span").html(plugin.reasons[key]);

			$(ul).append(li);
		}

		// Listen for deactivate
		$("#the-list [data-slug='" + plugin.slug + "'] .deactivate>a").on("click", function (event) {
			self.onDeactivateClicked(event, plugin);
		});
	}

	twz.DeactivateFeedbackForm.prototype.onDeactivateClicked = function (event, plugin) {
		this.deactivateURL = event.target.href;

		event.preventDefault();

		if (!this.dialog)
			this.dialog = $(this.element).remodal();
		this.dialog.open();
	}

	twz.DeactivateFeedbackForm.prototype.onSubmit = function (event, plugin) {
		var element = this.element;
		var strings = plugin.strings;
		var self = this;
		var data = $(element).find("form").serialize();

		$(element).find("button, input[type='submit']").prop("disabled", true);

		if ($(element).find("input[name='reason']:checked").length) {
			$(element).find("input[type='submit']").val(strings.thank_you);

			$.ajax({
				type: "POST",
				url: plugin.server_url,
				data: data,
				complete: function () {
					window.location.href = self.deactivateURL;
				}
			});
		}
		else {
			$(element).find("input[type='submit']").val(strings.please_wait);
			window.location.href = self.deactivateURL;
		}

		event.preventDefault();
		return false;
	}

	$(document).ready(function () {
		for (var i = 0; i < twz_software_deactivate_feedback_plugins.length; i++) {
			var plugin = twz_software_deactivate_feedback_plugins[i];
			new twz.DeactivateFeedbackForm(plugin);
		}
	});

})(jQuery);