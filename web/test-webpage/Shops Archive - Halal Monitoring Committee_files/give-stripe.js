/**
 * Give - Stripe Gateway Add-on JS
 */

var give_global_vars, give_stripe_vars;
var stripe = Stripe( give_stripe_vars.publishable_key );
if ( give_stripe_vars.stripe_account_id ) {
	stripe = Stripe( give_stripe_vars.publishable_key, { stripeAccount: give_stripe_vars.stripe_account_id } );
}

document.addEventListener( 'DOMContentLoaded', function( e ) {

	// Register Variables.
	var card                 = {};
	var cardElements         = [];
	var defaultGateway       = '';
	var globalCardElements   = [];
	var cardElementSelectors = [];
	var forms                = document.querySelectorAll( '.give-form' );

	// Loop through the number of forms on the page.
	forms.forEach( function( form_element ) {

		var elements       = stripe.elements();
		var formID         = form_element.querySelector( 'input[name="give-form-id"]').value;
		var defaultGateway = form_element.querySelector( '.give-gateway:checked').value;

		if ('single' === give_stripe_vars.cc_fields_format) {
			cardElementSelectors = [ '#give-stripe-single-cc-fields-' + formID ]
		} else if ('multi' === give_stripe_vars.cc_fields_format) {
			cardElementSelectors = [ '#give-card-number-field-' + formID, '#give-card-expiration-field-' + formID ,'#give-card-cvc-field-' + formID ]
		}

		// Create Card Elements for each form.
		cardElements = giveStripePrepareCardElements( formID, elements );

		// Prepare Card Elements for each form on a single page.
		globalCardElements[ formID ] = [];
		cardElementSelectors.forEach( function( selector, index ) {
			globalCardElements[formID][index]                  = [];
			globalCardElements[formID][index]['item']          = cardElements[index];
			globalCardElements[formID][index]['selector']      = selector;
			globalCardElements[formID][index]['isCardMounted'] = false;
		});

		// Loop through each form and fetch the gateway input element.
		form_element.querySelectorAll('.give-gateway').forEach( function( gateway_element, index ) {

			// Mount and Un-mount card elements when ajax completes based on gateway selected.
			gateway_element.addEventListener('change', function() {

				// Set default gateway.
				if ( gateway_element.checked ) {
					defaultGateway = gateway_element.value;
				}

				jQuery( document ).ajaxComplete( function( event, xhr, settings ) {

					if ( form_element.querySelector( '.give-gateway-option-selected .give-gateway').value === 'stripe' ) {

						// Mount card elements when stripe is the selected gateway.
						giveStripeMountCardElements( globalCardElements[formID] );
					}else {

						// Un-mount card elements when stripe is not the selected gateway.
						giveStripeUnmountCardElements( globalCardElements[formID] );
					}

					giveStripeTriggerFloatLabels( form_element );

				});

			});

		});

		// Mount Card Elements, if default gateway is stripe.
		if ( 'stripe' === defaultGateway ) {
			giveStripeMountCardElements( globalCardElements[formID] );
		} else if ( give_stripe_vars.stripe_card_update ) {
			giveStripeMountCardElements( globalCardElements[formID] );
		} else {
			giveStripeUnmountCardElements( globalCardElements[formID] );
		}

		// Convert normal fields to float labels.
		giveStripeTriggerFloatLabels( form_element );

		// Process Donation using Stripe Elements on form submission.
		jQuery( 'body' ).on( 'submit', '.give-form', function( event ) {

			var $form    = jQuery( this );
			var $form_id = $form.find( 'input[name="give-form-id"]').val();

			if ( 'stripe' === $form.find( 'input.give-gateway:checked' ).val() || give_stripe_vars.stripe_card_update ) {
				event.preventDefault();
				$form.addClass( 'stripe-checkout' );
				give_stripe_process_card( $form, globalCardElements[$form_id][0].item );
			}

		});

	});

	/**
	 * Trigger Float Labels when enabled.
	 *
	 * @param {object} form Form Object.
	 *
	 * @since 2.0
	 */
	function giveStripeTriggerFloatLabels( form ) {

		// Process it when float labels is enabled.
		if ( form.classList.contains( 'float-labels-enabled') ) {
			var formID = form.querySelector( 'input[name="give-form-id"]').value;

			form.querySelectorAll('.give-stripe-cc-field-wrap').forEach(function (element, index) {

				var ccLabelSelector = element.querySelector('label');
				var ccInnerDivSelector = element.querySelector('div');
				var ccInputSelector = element.querySelector('.give-stripe-cc-field');
				var ccWrapSelector = ccLabelSelector.parentElement;

				if (!ccLabelSelector.classList.contains('give-fl-label')) {
					ccLabelSelector.className = ccLabelSelector.classList + ' give-fl-label';
				}

				if (!ccInputSelector.classList.contains('give-fl-label')) {
					ccInputSelector.className = ccInputSelector.classList + ' give-fl-input';
				}

				if (!ccInnerDivSelector.classList.contains('give-fl-wrap give-fl-wrap-input give-fl-is-required')) {
					ccInnerDivSelector.className = ccInnerDivSelector.classList + ' give-fl-wrap give-fl-wrap-input give-fl-is-required';
				}

				globalCardElements[formID].forEach( function(globalElement) {

					if (globalElement.selector.indexOf( ccInputSelector.id ) > 0) {

						globalElement.item.on('change', function(e) {

							if (
								( e.empty === false || e.complete === true ) &&
								!ccWrapSelector.classList.contains('give-fl-is-active')
							) {
								ccWrapSelector.className = ccWrapSelector.classList + ' give-fl-is-active';
							} else if ( e.empty === true && e.complete === false) {
								ccWrapSelector.classList.remove('give-fl-is-active');
								ccWrapSelector.className = ccWrapSelector.classList;
							}
						});
					}
				});

			});
		}
	}

	/**
	 * Mount Card Elements
	 *
	 * @param {array} cardElements List of card elements to be mounted.
	 *
	 * @since 1.6
	 */
	function giveStripeMountCardElements( cardElements = [] ) {

		var cardElementsLength = Object.keys( cardElements ).length;

		// Assign any card element to variable to create token.
		if ( cardElementsLength > 0 ) {
			card = cardElements[0].item;
		}

		// Mount required card elements.
		cardElements.forEach( function( value, index ) {
			if ( false === value.isCardMounted ) {
				value.item.mount( value.selector );
				value.isCardMounted = true;
			}
		});
	}

	/**
	 * Un-mount Card Elements
	 *
	 * @param {array} cardElements List of card elements to be unmounted.
	 *
	 * @since 1.6
	 */
	function giveStripeUnmountCardElements( cardElements = [] ) {

		// Un-mount required card elements.
		cardElements.forEach( function( value, index ) {
			if ( true === value.isCardMounted ) {
				value.item.unmount();
				value.isCardMounted = false;
			}
		});
	}

	/**
	 * Create required card elements.
	 *
	 * @param {object} elements Stripe Element.
	 * @param {int}    formID   Donation Form ID.
	 *
	 * @since 1.6
	 *
	 * @returns {array}
	 */
	function giveStripePrepareCardElements( formID, elements ) {

		var prepareCardElements = [];
		var baseStyles          = give_stripe_vars.element_base_styles;

		// Mount CC Fields based on the settings.
		if ( 'multi' === give_stripe_vars.cc_fields_format ) {

			var cardNumber = [];
			var cardExpiry = [];
			var cardCvc    = [];

			var elementStyles = {
				base: baseStyles,
			};

			var elementClasses = {
				focus: 'focus',
				empty: 'empty',
				invalid: 'invalid',
			};


			cardNumber = elements.create(
				'cardNumber',
				{
					style: elementStyles,
					classes: elementClasses,
					placeholder: give_stripe_vars.card_number_placeholder_text,
				}
			);

			// Update Card Type for Stripe Multi Fields.
			cardNumber.addEventListener('change', function (event) {

				// Workaround for class name of Diners Club Card.
				var brand = ('diners' === event.brand) ? 'dinersclub' : event.brand;

				// Add Brand to card type wrapper to display specific brand logo based on card number.
				document.querySelector('.card-type').className = 'card-type ' + brand;
			});

			cardExpiry = elements.create(
				'cardExpiry',
				{
					style: elementStyles,
					classes: elementClasses,
				}
			);

			cardCvc = elements.create(
				'cardCvc',
				{
					style: elementStyles,
					classes: elementClasses,
				}
			);

			prepareCardElements.push( cardNumber, cardExpiry, cardCvc );

		} else if ('single' === give_stripe_vars.cc_fields_format) {

			var card = elements.create(
				'card',
				{
					style: {
						base: baseStyles,
						invalid: {
							color: "#E25950"
						}
					},
					hidePostalCode: !!(give_stripe_vars.checkout_address),
				}
			);

			prepareCardElements.push( card );

		}

		return prepareCardElements;

	}

	/**
	 * Stripe Response Handler
	 *
	 * @see https://stripe.com/docs/tutorials/forms
	 *
	 * @param {object} $form    Form Object.
	 * @param {object} response Response Object containing token.
	 */
	function give_stripe_response_handler( $form, response ) {

		// Append Token to Form HTML for form submission.
		$form.append( '<input type="hidden" name="give_stripe_token" value="' + response.id + '" />' );

		// Submit the form.
		$form.get(0).submit();

	}

	/**
	 * Stripe Process CC
	 *
	 * @param {object} $form Form Object.
	 * @param {object} card  Card Object.
	 *
	 * @returns {boolean}
	 */
	function give_stripe_process_card( $form, card ) {

		var state = '';
		var $form_id = $form.find( 'input[name="give-form-id"]').val();
		var $form_submit_btn = $form.find('[id^=give-purchase-button]');

		// disable the submit button to prevent repeated clicks.
		$form.find('[id^=give-purchase-button]').attr('disabled', 'disabled');

		if ( $form.find('.billing_country').val() === 'US' ) {
			state = $form.find( '[id^=card_state_us]' ).val();
		} else if ( $form.find( '.billing_country' ).val() === 'CA' ) {
			state = $form.find( '[id^=card_state_ca]' ).val();
		} else {
			state = $form.find( '[id^=card_state_other]').val();
		}

		// Validate card state & country data if present.
		if ( typeof $form.find( '[id^=card_state_us]' ).val() !== 'undefined') {

			if ($form.find('.billing_country').val() === 'US') {
				state = $form.find('[id^=card_state_us]').val();
			} else if ($form.find('.billing_country').val() === 'CA') {
				state = $form.find('[id^=card_state_ca]').val();
			} else {
				state = $form.find('[id^=card_state_other]').val();
			}

		} else {
			state = $form.find('.card_state').val();
		}

		// createToken returns immediately - the supplied callback submits the form if there are no errors.
		stripe.createToken( card ).then( function( result ) {

			if ( result.error ) {

				var error = '<div class="give_errors"><p class="give_error">' + result.error.message + '</p></div>';

				// re-enable the submit button.
				$form_submit_btn.attr( 'disabled', false );

				// Hide the loading animation.
				jQuery( '.give-loading-animation' ).fadeOut();

				// Display Error on the form.
				$form.find( '[id^=give-stripe-payment-errors-' + $form_id + ']' ).html( error );

				// Reset Donate Button.
				if ( give_global_vars.complete_purchase ) {
					$form_submit_btn.val( give_global_vars.complete_purchase );
				} else {
					$form_submit_btn.val( $form_submit_btn.data( 'before-validation-label' ) );
				}

			} else {

				// Send source to server for processing payment.
				give_stripe_response_handler( $form, result.token );
			}
		});

		return false; // Submit from callback.
	}

});