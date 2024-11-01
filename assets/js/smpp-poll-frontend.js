(function ($) {
	(function () {
		jQuery(function () {
			return jQuery('[data-toggle]').on('click', function () {
				var toggle;
				toggle = $(this).addClass('active').attr('data-toggle');
				jQuery(this).siblings('[data-toggle]').removeClass('active');
				return jQuery('.surveys').removeClass('grid list').addClass(toggle);
			});
		});
	}.call(this));



	if (document.querySelector('smpp_survey-item-action-disabled')) {
		document.querySelector('.smpp_survey-vote-button').addEventListener('click', function () {
			this.classList.add('smpp_fill-option');
		});
	}

})(jQuery);