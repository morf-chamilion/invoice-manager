/**
 * Select2 JS Class.
 *
 * Handles form select behaviour based on user interactions.
 */
class AppSelect2 {
	constructor() {
		// Init
		this.boot();
		this.handleHydration();
	}

	/**
	 * Boot manager.
	 */
	boot() {
		this.templateIconLayout();
	}

	/**
	 * Handle hydration.
	 */
	handleHydration() {
		// JQuery Select2 by default will not submit any input keys with empty values
		// The user will not be able to clear out all the field values as of this.
		// We manually insert keys with empty arrays to counter this behaviour.
		(function () {
			$('form').each(function () {
				$(this).on('submit', function () {
					$(this).find(':input').each(function () {
						if ($(this).hasClass('select-2') && $(this).attr('multiple')) {
							// we only need to really apply the fix for arrays
							if (Array.isArray($(this).val()) && $(this).val().length == 0) {
								if (!$(this).attr('name')) return;
								$(this).closest('form').append(`<input name="${$(this).attr('name')}" type="hidden" />`)
							}
						}
					});
				})
			});
		})();
	}

	templateIconLayout() {
		const optionFormat = function (item) {
			const imgUrl = item.element?.getAttribute('data-select2-icon');
			let span = document.createElement('span');

			if (!item.id || !imgUrl) {
				return item.text;
			}

			const template = `<img src="${imgUrl}" class="rounded-circle h-30px w-30px me-2 object-fit-cover" alt="${item.text.trim()}" loading="lazy"/>${item.text}`;

			span.innerHTML = template;

			return $(span);
		}

		$('[data-control="select2-icon"]').select2({
			templateSelection: optionFormat,
			templateResult: optionFormat,
			allowClear: true,
		});
	}
}

// Initialize
const select2 = new AppSelect2();