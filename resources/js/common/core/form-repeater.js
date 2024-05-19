/**
 * Form Repeater JS Class.
 *
 * Handles form repeater behaviour based on user interactions.
 */
class AppFormRepeater {
	constructor() {
		this.boot();
		this.handleHydration();
	}

	/**
	 * Boot manager.
	 */
	boot() {
		$.when($.ready).then(function () {
			$('.form-repeater').repeater({
				initEmpty: false,

				show: function () {
					$(this).slideDown();

					const repeater = $(this).parents('.form-repeater');
					const limitCount = repeater.data('repeater-max-items');
					const itemCount = repeater.find('div[data-repeater-item]').length;

					if (itemCount >= limitCount) {
						$(repeater).find('button[data-repeater-create]').prop('disabled', true);
						$(repeater).find('button[data-repeater-create]').html('<i class="fa fa-ban fs-6"></i> Max item count reached');
					}
				},

				hide: function (deleteElement) {
					$(this).slideUp(deleteElement);

					const repeater = $(this).parents('.form-repeater');
					const limitCount = repeater.data('repeater-max-items');
					const itemCount = repeater.find('div[data-repeater-item]').length;

					if (itemCount <= limitCount) {
						$(repeater).find('input[data-repeater-create]').show('slow');

						$(repeater).find('button[data-repeater-create]').prop('disabled', false);
						$(repeater).find('button[data-repeater-create]').html('<i class="fa fa-plus fs-6"></i> Add');
					}
				}
			});
		})
	}

	/**
	 * Handle hydration.
	 */
	handleHydration() {
		// JQuery RepeaterJs by default will not submit input keys with empty values
		// The user will not be able to clear out all the field values as of this.
		// We manually insert keys with empty arrays to counter this behaviour.
		(function () {
			$('form').each(function () {
				$(this).find('button[type="submit"]').on('click', function () {
					$('*[data-repeater-list]').each(function () {
						let repeaterId = $(this).attr('data-repeater-list');
						let allValuesEmpty = true;

						$(this).find(':input').each(function () {
							if ($(this).val().trim() !== '') {
								allValuesEmpty = false;
							}
						})

						if (allValuesEmpty) {
							$('form#resource_form').append(`<input name="${repeaterId}" type="hidden" />`)
						}
					})
				})
			});
		})();
	}
}

// Initialize
const formRepeater = new AppFormRepeater();