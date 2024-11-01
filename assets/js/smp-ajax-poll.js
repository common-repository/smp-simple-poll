jQuery(document).ready(function () {


	if (jQuery(".smp_survey-item-action-disabled").length > 0) {
		jQuery(".smp_survey-item-action-disabled .smp_survey-vote-button").addClass("smp_disabled-button");
		jQuery(".smp_survey-item-action-disabled .smp_disabled-button").removeClass("smp_survey-vote-button");
	}


	let getUniqIDs = [];
	if (localStorage.getItem(`set_uniq_ids`) === null) {
		localStorage.setItem(`set_uniq_ids`, ' , ');
	}
	jQuery('.smp_option-name.live').on('click', function () {
		let getUniqID = jQuery(this).siblings(".smp_survey-item-id").val();
		jQuery(this).addClass(`voted-for-${getUniqID}`);
		jQuery(`voted-for-${getUniqID}.smp_option-name.live`).removeClass('smp_fill-option');
		jQuery(this).addClass('smp_fill-option');
		getUniqIDs.push(`${getUniqID}`);
		localStorage.setItem(`set_uniq_ids`, getUniqIDs.toString());
	});

	localStorage.getItem('set_uniq_ids').split(',').forEach(function (value) {
		var activeOptionId = localStorage.getItem(value);
		if (jQuery(".smp_survey-item-action-disabled").length > 0) {
			jQuery(`.smp_survey-item-action-disabled input[value=${value}]`).siblings('[type=button]').addClass('smp_fill-option');
		}
	});

	jQuery('.smp_survey-item').each(function () {
		var smp_item = jQuery(this);
		jQuery(this).find('.smp_survey-vote-button.live').click(function () {

			jQuery(smp_item).parent().find('.smp_survey-item').each(function () {
				jQuery(this).find('.smp_survey-vote-button').attr('disabled', 'yes');

			});

			var smp_btn = jQuery(this);
			jQuery('.smp_survey-vote-button').closest('.smp_container').removeClass('activated');
			smp_btn.closest('.smp_container').addClass(`activated`);

			var data = {
				'action': 'smp_vote',
				'option_id': jQuery(smp_item).find('.smp_survey-item-id').val(),
				'poll_id': jQuery(smp_item).find('.smp_poll-id').val() // We pass php values differently!
			};

			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			jQuery.post(smp_poll_ajax_obj.ajax_url, data, function (response) {

				var smp_json = jQuery.parseJSON(response);
				console.log(response);

				let vote_percentage = ((100 * smp_json.total_opt_vote_count) / smp_json.total_vote_count).toFixed(2);

				jQuery(smp_item).parent().find('.smp_survey-item').each(function () {
					jQuery(this).find('.smp_survey-vote-button').addClass('smp_scale_hide');
				});


				if (smp_item.hasClass('public') || smp_item.hasClass('public_after_vote')) {
					jQuery('.temp-hide').removeClass('temp-hide');
					jQuery(`.activated .public .smp_survey-progress-fg, .activated .public_after_vote .smp_survey-progress-fg`).attr('style', 'width:' + Math.abs(100 - vote_percentage) + '%');

					jQuery(smp_item).find(`.smp_survey-progress-fg`).attr('style', 'width:' + vote_percentage + '%');

					jQuery(`.activated .public .smp_survey-progress-label, .activated .public_after_vote .smp_survey-progress-label`).text(Math.abs(100 - vote_percentage).toFixed(2) + '%');

					jQuery(smp_item).find(`.smp_survey-progress-label`).text(vote_percentage + '%');

					jQuery(`.activated .smp_survey-total-vote span span`).text(smp_json.total_vote_count);
				}

				jQuery('.activated .smp_user-partcipeted').text('Thank you for participating.');


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

