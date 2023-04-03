jQuery(document).ready(function($){	

    $('#prop_type').trigger('ready');

    $(document).on('change', '#prop_type', function(e) {
        new update_fields_on_state_change(e, $);
        // alert(e);
    });

    

});

class update_fields_on_state_change 
{
	constructor(e, $) {
		// if (this.request) {

		// 	alert(this.request);
		// 	// if a recent request has been made abort it
		// 	this.request.abort();
		// }

		// get the #prop_type select field, and remove all exisiting choices
		var prop_type = $('#prop_type');

		// get the target of the event and then get the value of that field
		var target = $(e.target);
		var term = target.val();
		// alert(state);
		if (!term) {
			// no state selected
			// don't need to do anything else
			// return;
		}

		// set and prepare data for ajax
		var data = {
			action: 'load_prop_field',
			term: term
		};

		// call the acf function that will fill in other values
		// like post_id and the acf nonce
        var details = $('#aqar-details');
		// make ajax request
		// instead of going through the acf.ajax object to make requests like in <5.7
		// we need to do a lot of the work ourselves, but other than the method that's called
		// this has not changed much
		$.ajax({
			url: ajax_aqar.ajaxurl,
			data: data,
			type: 'post',
            beforeSend: function() {
                // setting a timeout
                $('.placeholder').addClass('loading');
                details.empty();
            }, 
			success: function(data) {
                details.html(data);
                $('.selectpicker').selectpicker();
            },
            error: function(xhr) {
                console.warn(xhr);
                details.append(xhr.statusText + xhr.responseText);
            }
		});

	}
}

jQuery(document).ready(function($){	
	$('#aqar-login-btn').on('click', function(e){
		e.preventDefault();
		var currnt = $(this);
		houzez_login( currnt );
	});
	
	$('#aqar-register-btn').on('click', function(e){
		e.preventDefault();
		var currnt = $(this);
		houzez_register( currnt );
	});
	var houzez_reCaptcha = parseInt(houzez_vars.houzez_reCaptcha);
	var houzez_login = function( currnt ) {
		var $form = currnt.parents('form');
		var $messages = $('#hz-login-messages');
	
		$.ajax({
			type: 'post',
			url: ajax_aqar.ajaxurl,
			dataType: 'json',
			data: $form.serialize(),
			beforeSend: function( ) {
				currnt.find('.houzez-loader-js').addClass('loader-show');
			},
			complete: function(){
				currnt.find('.houzez-loader-js').removeClass('loader-show');
			},
			success: function( response ) {
				if( response.success ) {
					$messages.empty().append('<div class="alert alert-success" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.msg +'</div>');
					
					window.location.replace( response.redirect_to );
	
				} else {
					$messages.empty().append('<div class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.msg +'</div>');
				}

				if(  response.show_otp &&  response.show_otp === 'yes' ) {
					$('#loin_user').val(response.user);
					$('#otp-form').show();
					$('#login-form').hide();
					
				}
	
				currnt.find('.houzez-loader-js').removeClass('loader-show');
	
				if(houzez_reCaptcha == 1) {
					$form.find('.g-recaptcha-response').remove();
					if( g_recaptha_version == 'v3' ) {
						houzezReCaptchaLoad();
					} else {
						houzezReCaptchaReset();
					}
				}
			},
			error: function(xhr, status, error) {
				var err = eval("(" + xhr.responseText + ")");
				console.log(err.Message);
			}
		})
	
	} // end houzez_login
	
	var houzez_register = function ( currnt ) {
	
		var $form = currnt.parents('form');
		var $messages = $('#hz-register-messages');
	
		$.ajax({
			type: 'post',
			url: ajax_aqar.ajaxurl,
			dataType: 'json',
			data: $form.serialize(),
			beforeSend: function( ) {
				currnt.find('.houzez-loader-js').addClass('loader-show');
			},
			complete: function(){
				currnt.find('.houzez-loader-js').removeClass('loader-show');
			},
			success: function( response ) {
				if( response.success ) {
					$messages.empty().append('<div class="alert alert-success" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.msg +'</div>');
				} else {
					$messages.empty().append('<div class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.msg +'</div>');
				}
				if(  response.success &&  response.user ) {
					$('#register_user').val(response.user);
					$('#otp-form-1').show();
					$('#register-form').hide();
				}
				// if( response.redirect_to ){
				// 	window.location.replace( response.redirect_to );
				// }

				currnt.find('.houzez-loader-js').removeClass('loader-show');
				if(houzez_reCaptcha == 1) {
					$form.find('.g-recaptcha-response').remove();
					if( g_recaptha_version == 'v3' ) {
						houzezReCaptchaLoad();
					} else {
						houzezReCaptchaReset();
					}
				}
			},
			error: function(xhr, status, error) {
				var err = eval("(" + xhr.responseText + ")");
				console.log(err.Message);
				console.log(error);
				console.log(xhr);
				
			}
		});
	}
	
	});