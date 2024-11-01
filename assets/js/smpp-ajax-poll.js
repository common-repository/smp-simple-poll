(function ($) {
	jQuery(document).ready(function () {
		if (jQuery(".smpp_survey-item-action-disabled").length > 0) {
			jQuery(".smpp_survey-item-action-disabled .smpp_survey-vote-button").addClass("smpp_disabled-button");
			jQuery(".smpp_survey-item-action-disabled .smpp_disabled-button").removeClass("smpp_survey-vote-button");
		}

		let getUniqIDs;

		if (localStorage.getItem(`set_uniq_ids`) === null) {
			getUniqIDs = [];
			localStorage.setItem(`set_uniq_ids`, ',');
		}
		else {
			getUniqIDs = localStorage.getItem(`set_uniq_ids`).split(',');
		}
		jQuery('.smpp_option-name.live').on('click', function () {
			let getUniqID = jQuery(this).siblings(".smpp_survey-item-id").val();
			jQuery(this).addClass(`voted-for-${getUniqID}`);
			jQuery(`voted-for-${getUniqID}.smpp_option-name.live`).removeClass('smpp_fill-option');
			jQuery(this).addClass('smpp_fill-option');
			getUniqIDs.push(`${getUniqID}`);
			localStorage.setItem(`set_uniq_ids`, getUniqIDs.toString());
		});

		localStorage.getItem('set_uniq_ids').split(',').forEach(function (value) {
			var activeOptionId = localStorage.getItem(value);
			if (jQuery(".smpp_survey-item-action-disabled").length > 0) {
				jQuery(`.smpp_survey-item-action-disabled input[value=${value}]`).siblings('[type=button]').addClass('smpp_fill-option');
			}
		});

		console.log(localStorage.getItem('set_uniq_ids'));

		jQuery('.smpp_survey-item').each(function () {
			var smpp_item = jQuery(this);
			jQuery(this).find('.smpp_survey-vote-button.live').click(function () {

				jQuery(smpp_item).parent().find('.smpp_survey-item').each(function () {
					jQuery(this).find('.smpp_survey-vote-button').attr('disabled', 'yes');

				});

				var smpp_btn = jQuery(this);
				jQuery('.smpp_survey-vote-button').closest('.smpp_container').removeClass('activated');
				smpp_btn.closest('.smpp_container').addClass(`activated`);

				var data = {
					'action': 'smpp_vote',
					'option_id': jQuery(smpp_item).find('.smpp_survey-item-id').val(),
					'poll_id': jQuery(smpp_item).find('.smpp_poll-id').val() // We pass php values differently!
				};


				// We can also pass the url value separately from ajaxurl for front end AJAX implementations
				jQuery.post(smpp_ajax_obj.ajax_url, data, function (response) {

					var smpp_json = jQuery.parseJSON(response);
					console.log(response);

					jQuery(smpp_item).parent().find('.smpp_survey-item').each(function () {
						jQuery(this).find('.smpp_survey-vote-button').addClass('smpp_scale_hide');
					});


					if (smpp_item.hasClass('public') || smpp_item.hasClass('public_after_vote')) {
						jQuery('.activated .temp-hide').removeClass('temp-hide');

						console.log(smpp_json.total_vote_percentage.length);
						for (let i = 0; i <= smpp_json.total_vote_percentage.length; i++) {
							jQuery(`.activated .public_after_vote.smpp-item-${i} .smpp_survey-progress-fg, .activated .public.smpp-item-${i} .smpp_survey-progress-fg`).attr('style', 'width:' + smpp_json.total_vote_percentage[i] + `%`);

							jQuery(`.activated .public.smpp-item-${i} .smpp_survey-progress-label, .activated .public_after_vote.smpp-item-${i} .smpp_survey-progress-label`).text(smpp_json.total_vote_percentage[i] + '%');
						}

						jQuery(`.activated .smpp_survey-total-vote span span`).text(smpp_json.total_vote_count);
					}

					jQuery('.activated .smpp_user-partcipeted').text('Thank you for participating.');


					function createCookie(name, value, days) {
						let expires = "";
						if (days) {
							let date = new Date();
							date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
							expires = "; expires=" + date.toGMTString();
						}
						document.cookie = name + "=" + value + expires + "; path=/";
					}
					createCookie(`is_voted_${data.poll_id}`, "1", 365);



				});

			});

		});

	});

})(jQuery);