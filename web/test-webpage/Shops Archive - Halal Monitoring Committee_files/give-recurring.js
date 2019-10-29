/**
 * Give Frontend Recurring JS
 *
 * Scripts function in frontend form.
 */
var Give_Recurring_Vars;

jQuery( document ).ready( function( $ ) {

	var doc = $( document );

	var Give_Recurring = {

		/**
		 * Initialize
		 */
		init: function() {

			var $body = $( 'body' );

			$body.on( 'click touchend', '.give-recurring-form-admin .give-donation-level-btn, .give-recurring-form-admin  .give-radio-input-level, .give-recurring-form-admin  give-select-level > option', this.notifyUserOnLevels );
			$body.on( 'change', '.give-select-level', this.notifyUserOnLevels );
			$body.on( 'keyup', '#give-amount', this.changeCustomAmount );

			Give_Recurring.confirm_subscription_cancellation();
			Give_Recurring.conditional_account_creation();

			$( '.give-recurring-period' ).on( 'click', function() {
				var $this = $( this );
				if ( $this.is( ':checked' ) ) {
					Give_Recurring.fetchRecurringPeriodText( $this );
				} else {
					$( '#give-recurring-modal-period-wrap' ).hide();
					$( '#give-recurring-modal-period' ).html( '' );
				}
			});

			$( '.give-recurring-donors-choice-period' ).on( 'change', function() {
				Give_Recurring.fetchRecurringPeriodText();
			});

			// Trigger formatting function when gateway changes.
			doc.on( 'give_gateway_loaded', this.conditionalPeriodDropdown );
			Give_Recurring.conditionalPeriodDropdown();
			Give_Recurring.changeGiveIsDonationRecurring();
		},

		/**
		 * Display Recurring Period Label in Modal PopUp.
		 *
		 * @param label Period Label.
		 *
		 * @since 1.5.8
		 */
		displayModalLabel: function( label ) {
			if ( label !== "once" && label !== "" ) {
				$( '#give-recurring-modal-period-wrap' ).show();
				$( '#give-recurring-modal-period' ).html( label.charAt(0).toUpperCase() + label.slice(1) );
			} else if ( label === "once" ) {
				$( '#give-recurring-modal-period-wrap' ).hide();
				$( '#give-recurring-modal-period' ).html( '' );
			}
		},

		/**
		 * Fetch Recurring Period Text
		 *
		 * @param $this this object.
		 *
		 * @since 1.5.8
		 */
		fetchRecurringPeriodText: function( $this ) {
			var $select              = document.querySelector('.give-recurring-donors-choice-period');
			var recurringPeriodLabel = '';

			if ( null !== $select ) {
				recurringPeriodLabel = $select.options[$select.selectedIndex].text;
			} else {
				recurringPeriodLabel = $this.data( 'period' );
			}

			Give_Recurring.displayModalLabel( recurringPeriodLabel );
		},

		/**
		 * Notify users with message about the type of donation based on level.
		 *
		 * @since 1.4
		 */
		notifyUserOnLevels: function() {

			var form = $( this ).closest( 'form' ), formID = form.find( 'input[name="give-form-id"]' ).val(),
				priceID = $( this ).data( 'price-id' );

			if ( undefined === priceID ) {
				priceID = $( this ).find( 'option:selected' ).data( 'price-id' );
			}

			if ( ! formID || undefined === priceID ) {
				return false;
			}

			jQuery.post( give_global_vars.ajaxurl, {
					action: 'give_recurring_notify_user_on_levels',
					formID: formID,
					priceID: priceID,
				},
				function( response ) {
					// Replace the HTML.
					$( '.give-recurring-multi-level-message' ).html( response.data.html );

					// update the hidden fields.
					form.find( ' ._give_is_donation_recurring' ).val( response.data.is_recurring );
					Give_Recurring.register_checkbox( form );

					// Display Modal Label when recurring is enabled.
					Give_Recurring.displayModalLabel( response.data.period_label );
				}
			);
		},

		changeGiveIsDonationRecurring: function () {
			$( '.give-form' ).on( 'change', '.give-recurring-donors-choice', function () {
				var give_is_donation_recurring = '0';
				var recurring_period = $( this ).find( '.give-recurring-period:checked' ).val();

				if ( 'undefined' !== typeof(
						recurring_period
					) && 'on' === recurring_period ) {
					give_is_donation_recurring = 1;
				}

				var form = $( this ).closest( 'form' );
				form.find( '._give_is_donation_recurring' ).val( give_is_donation_recurring );
				Give_Recurring.register_checkbox( form );
			} );
		},

		/**
		 * Change custom amount in message on typing.
		 *
		 * @since 1.4
		 */
		changeCustomAmount: function() {
			var customAmount = '1.00';
			if ( '' !== $( this ).val() ) {
				customAmount = $( this ).val();
			}

			// If there is no decimal then add decimal to the custom amount.
			if ( - 1 === customAmount.indexOf( '.' ) ) {
				customAmount = customAmount + '.00';
			}

			$( '.give-recurring-multi-level-message span.amount' ).html( customAmount );
		},

		/**
		 * Toggle account creation fields if donor elects not to give recurring.
		 *
		 * If email access is not enabled, no guest donations are allowed.
		 * Therefore, if the donor doesn't give recurring no account is necessary.
		 */
		conditional_account_creation: function() {

			// Only w/o Email Access Enabled
			if ( Give_Recurring_Vars.email_access ) {
				return false;
			}

			// fire once donation form gateway is change.
			doc.on( 'give_gateway_loaded', function( ev, response, form_id ) {
				// Trigger float-labels
				Give_Recurring.register_checkbox( $( 'form#' + form_id ) );
			} );

			// On Page Load: When page loads loop through each form and show hide fields.
			$( 'form[id^=give-form].give-recurring-form' ).each( function () {
				Give_Recurring.register_checkbox( $( this ) );
			} );

			// Preserve Create account checkbox checked on login cancel ajax.
			$( document ).ajaxComplete( function ( event, xhr, settings ) {
				var get_action = Give_Recurring.get_parameter( 'action', settings.data );
				if ( 'give_cancel_login' === get_action ) {
					var form_id = Give_Recurring.get_parameter( 'form_id', settings.data );
					Give_Recurring.register_checkbox( $( 'form#give-form-' + form_id ) );
				}
			} );
		},

		/**
		 * Get specific parameter value from Query string.
		 *
		 * @param string parameter Parameter of query string.
		 * @param object data Set of data.
		 *
		 * @return bool
		 */
		get_parameter: function ( parameter, data ) {

			if ( ! parameter ) {
				return false;
			}

			if ( ! data ) {
				data = window.location.href;
			}

			var parameter = parameter.replace( /[\[]/, "\\\[" ).replace( /[\]]/, "\\\]" );
			var expr = parameter + "=([^&#]*)";
			var regex = new RegExp( expr );
			var results = regex.exec( data );

			if ( null !== results ) {
				return results[1];
			} else {
				return false;
			}
		},

		/**
		 * Disable Enable the register user checkbox
		 *
		 * @since 1.5.5
		 *
		 * @param form
		 */
		register_checkbox: function( form ) {

			// Only w/o Email Access Enabled
			if ( Give_Recurring_Vars.email_access ) {
				return false;
			}

			// check if guest donation is allow or not.
			var recurring_logged = form.find( '.give-logged-in-only' ).val();

			// if guest donation is not allow then return false.
			if ( 'undefined' !== typeof( recurring_logged ) && 1 === parseInt( recurring_logged ) ) {
				var is_recurring = form.find( '._give_is_donation_recurring' ).val();

				if ( 'undefined' !== typeof( is_recurring ) && 1 === parseInt( is_recurring ) ) {
					Give_Recurring.disable_register_checkbox( form );
				} else {
					Give_Recurring.enable_register_checkbox( form );
				}
			}
		},

		/**
		 * Remove the Disable checkbox option so that user can check and uncheck if s/he want to register or not
		 *
		 * @since 1.5.5
		 *
		 * @param form
		 */
		enable_register_checkbox: function ( form ) {
			var create_account_html = form.find( '[name="give_create_account"]' );
			create_account_html.removeClass( 'give-disabled' );
			create_account_html.closest( 'span' ).removeClass( 'hint--top hint--bounce' );
		},

		/**
		 * Add the Disable checkbox option so that user can not  check and uncheck if s/he want to register or not
		 *
		 * @since 1.5.5
		 *
		 * @param form
		 */
		disable_register_checkbox: function ( form ) {
			var create_account_html = form.find( '[name="give_create_account"]' );
			create_account_html.attr( 'checked', true );
			create_account_html.addClass( 'give-disabled' );
			create_account_html.closest( 'span' ).addClass( 'hint--top hint--bounce' );
		},

		/**
		 * Confirm Cancellation
		 */
		confirm_subscription_cancellation: function() {

			$( '.give-cancel-subscription' ).on( 'click touchend', function() {
				var response = confirm( Give_Recurring_Vars.messages.confirm_cancel );
				// Cancel form submit if user rejects confirmation.
				if ( response !== true ) {
					return false;
				}
			} );

		},

		/**
		 * Conditional "Period" dropdown
		 *
		 * Some gateways don't support "daily" frequency.
		 *
		 * @since 1.5
		 */
		conditionalPeriodDropdown: function( response ) {

			// Loop through selected gateways on page.
			$( '.give-gateway-option-selected' ).each( function() {

				var $form = $( this ).parents( '.give-form' ),
					gateway = $( this ).find( 'input' ).val(),
					period_select = $form.find( '.give-recurring-donors-choice-period' ),
					period = period_select.val(),
					day_option = $form.find( '.give-recurring-donors-choice-period option[value="day"]' );

				// Authorize doesn't support daily.
				if ( 'authorize' === gateway ) {

					// Only proceed if day is selected.
					if ( 'day' !== period ) {
						return;
					}

					// Disable and select next option.
					day_option.prop( 'disabled', true ).next().attr( 'selected', 'selected' );

					// Only show alert when switching gatways and not on page load.
					if ( 'undefined' !== typeof response ) {
						alert( Give_Recurring_Vars.messages.daily_forbidden );
					}

				} else {

					// Ensure that daily option is disabled.
					day_option.prop( 'disabled', false );

				}

			} );

		}

	};

	Give_Recurring.init();

} );