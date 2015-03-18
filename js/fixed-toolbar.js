jQuery(document).ready( function($) {
	var stamp, visibility, $submitButtons, updateVisibility, updateText,
		sticky = '',
		last = 0,
		co = $('#content'),
		$document = $(document),
		$editSlugWrap = $('#edit-slug-box'),
		postId = $('#post_ID').val() || 0,
		$submitpost = $('#submitpost'),
		releaseLock = true,
		$postVisibilitySelect = $('#post-visibility-select'),
		$timestampdiv = $('#timestampdiv'),
		$postStatusSelect = $('#post-status-select');
	// fixed-submitdiv
	if ( $('#fixed-submitdiv').length ) {
		//$('html').addClass('fixed-toolbar-bottom');
		$('html').addClass('fixed-toolbar-top');
		stamp = $('#timestamp').html();
		visibility = $('#post-visibility-display').html();

		updateVisibility = function() {
			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'public' ) {
				$('#sticky').prop('checked', false);
				$('#sticky-span').hide();
			} else {
				$('#sticky-span').show();
			}
			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'password' ) {
				$('#password-span').hide();
			} else {
				$('#password-span').show();
			}
		};

		updateText = function() {

			if ( ! $timestampdiv.length )
				return true;

			var attemptedDate, originalDate, currentDate, publishOn, postStatus = $('#post_status'),
				optPublish = $('option[value="publish"]', postStatus), aa = $('#aa').val(),
				mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val();

			attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
			originalDate = new Date( $('#hidden_aa').val(), $('#hidden_mm').val() -1, $('#hidden_jj').val(), $('#hidden_hh').val(), $('#hidden_mn').val() );
			currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val() );

			if ( attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn ) {
				$timestampdiv.find('.timestamp-wrap').addClass('form-invalid');
				return false;
			} else {
				$timestampdiv.find('.timestamp-wrap').removeClass('form-invalid');
			}

			if ( attemptedDate > currentDate && $('#original_post_status').val() != 'future' ) {
				publishOn = postL10n.publishOnFuture;
				$('#publish').val( postL10n.schedule );
			} else if ( attemptedDate <= currentDate && $('#original_post_status').val() != 'publish' ) {
				publishOn = postL10n.publishOn;
				$('#publish').val( postL10n.publish );
			} else {
				publishOn = postL10n.publishOnPast;
				$('#publish').val( postL10n.update );
			}
			if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) { //hack
				$('#timestamp').html(stamp);
			} else {
				$('#timestamp').html(
					publishOn + ' <b>' +
					postL10n.dateFormat.replace( '%1$s', $('option[value="' + $('#mm').val() + '"]', '#mm').text() )
						.replace( '%2$s', jj )
						.replace( '%3$s', aa )
						.replace( '%4$s', hh )
						.replace( '%5$s', mn ) +
						'</b> '
				);
			}

			if ( $postVisibilitySelect.find('input:radio:checked').val() == 'private' ) {
				$('#publish').val( postL10n.update );
				if ( 0 === optPublish.length ) {
					postStatus.append('<option value="publish">' + postL10n.privatelyPublished + '</option>');
				} else {
					optPublish.html( postL10n.privatelyPublished );
				}
				$('option[value="publish"]', postStatus).prop('selected', true);
				$('#misc-publishing-actions .edit-post-status').hide();
			} else {
				if ( $('#original_post_status').val() == 'future' || $('#original_post_status').val() == 'draft' ) {
					if ( optPublish.length ) {
						optPublish.remove();
						postStatus.val($('#hidden_post_status').val());
					}
				} else {
					optPublish.html( postL10n.published );
				}
				if ( postStatus.is(':hidden') )
					$('#misc-publishing-actions .edit-post-status').show();
			}
			$('#post-status-display').html($('option:selected', postStatus).text());
			if ( $('option:selected', postStatus).val() == 'private' || $('option:selected', postStatus).val() == 'publish' ) {
				$('#save-post').hide();
			} else {
				$('#save-post').show();
				if ( $('option:selected', postStatus).val() == 'pending' ) {
					$('#save-post').show().val( postL10n.savePending );
				} else {
					$('#save-post').show().val( postL10n.saveDraft );
				}
			}
			return true;
		};

		$( '#visibility .edit-visibility').click( function () {
			if ( $postVisibilitySelect.is(':hidden') ) {
				updateVisibility();
				$postVisibilitySelect.slideDown('fast').find('input[type="radio"]').first().focus();
				$(this).hide();
			}
			return false;
		});

		$postVisibilitySelect.find('.cancel-post-visibility').click( function( event ) {
			$postVisibilitySelect.slideUp('fast');
			$('#visibility-radio-' + $('#hidden-post-visibility').val()).prop('checked', true);
			$('#post_password').val($('#hidden-post-password').val());
			$('#sticky').prop('checked', $('#hidden-post-sticky').prop('checked'));
			$('#post-visibility-display').html(visibility);
			$('#visibility .edit-visibility').show().focus();
			updateText();
			event.preventDefault();
		});

		$postVisibilitySelect.find('.save-post-visibility').click( function( event ) { // crazyhorse - multiple ok cancels
			$postVisibilitySelect.slideUp('fast');
			$('#visibility .edit-visibility').show();
			updateText();

			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'public' ) {
				$('#sticky').prop('checked', false);
			} // WEAPON LOCKED

			if ( $('#sticky').prop('checked') ) {
				sticky = 'Sticky';
			} else {
				sticky = '';
			}

			$('#post-visibility-display').html(	postL10n[ $postVisibilitySelect.find('input:radio:checked').val() + sticky ]	);
			event.preventDefault();
		});

		$postVisibilitySelect.find('input:radio').change( function() {
			updateVisibility();
		});

		$timestampdiv.siblings('a.edit-timestamp').click( function( event ) {
			if ( $timestampdiv.is( ':hidden' ) ) {
				$timestampdiv.slideDown('fast');
				$('#mm').focus();
				$(this).hide();
			}
			event.preventDefault();
		});

		$timestampdiv.find('.cancel-timestamp').click( function( event ) {
			$timestampdiv.slideUp('fast').siblings('a.edit-timestamp').show().focus();
			$('#mm').val($('#hidden_mm').val());
			$('#jj').val($('#hidden_jj').val());
			$('#aa').val($('#hidden_aa').val());
			$('#hh').val($('#hidden_hh').val());
			$('#mn').val($('#hidden_mn').val());
			updateText();
			event.preventDefault();
		});

		$timestampdiv.find('.save-timestamp').click( function( event ) { // crazyhorse - multiple ok cancels
			if ( updateText() ) {
				$timestampdiv.slideUp('fast');
				$timestampdiv.siblings('a.edit-timestamp').show();
			}
			event.preventDefault();
		});

		$('#post').on( 'submit', function( event ) {
			if ( ! updateText() ) {
				event.preventDefault();
				$timestampdiv.show();

				if ( wp.autosave ) {
					wp.autosave.enableButtons();
				}

				$('#publishing-action .spinner').hide();
			}
		});

		$postStatusSelect.siblings('a.edit-post-status').click( function( event ) {
			if ( $postStatusSelect.is( ':hidden' ) ) {
				$postStatusSelect.slideDown('fast').find('select').focus();
				$(this).hide();
			}
			event.preventDefault();
		});

		$postStatusSelect.find('.save-post-status').click( function( event ) {
			$postStatusSelect.slideUp('fast').siblings('a.edit-post-status').show();
			updateText();
			event.preventDefault();
		});

		$postStatusSelect.find('.cancel-post-status').click( function( event ) {
			$('#post-status-select').slideUp('fast').siblings( 'a.edit-post-status' ).show().focus();
			$('#post_status').val( $('#hidden_post_status').val() );
			updateText();
			event.preventDefault();
		});
	} // end fixed-submitdiv
});