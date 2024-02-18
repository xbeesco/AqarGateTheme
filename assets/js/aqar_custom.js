jQuery(document).ready(function($){	

	//attach a change event listener to the radio buttons -- could give them a common class
    $(':radio[name=aqar_author_type_id]').on('change', function() {

		let _val = $(this).val();
        //reset labels
		if( _val == 2 ) {
			$('#change-lable').text('رقم الهوية');
		} else {
			$('#change-lable').text('رقم الهوية');
		}
    });

	var screen_1 = $('#register-screen-1');
	var screen_2 = $('#aq-register-form');
	var $messages = $('#hz-register-messages');

	$('#next-register-btn').on('click', function(e){
		e.preventDefault();
		var currnt = $(this);
		var form = currnt.parents('form');
		const aqar_author_type_id = $("input[name=aqar_author_type_id]:checked").val();
        const authorid = $("input[name=id]").val();

		if( aqar_author_type_id === undefined ) {
			alert('اختار نوع التسجيل اولا');
			return;
		}
		// الدخول عن طريق نفاذ
		if( aqar_author_type_id === '1' ||  
		    aqar_author_type_id === '2' ||
			aqar_author_type_id === '3' ||
			aqar_author_type_id === '4' 
		  ) 
		{
			if(  authorid== null ||  authorid == ""  ) {
				alert('ادخل رقم الهوية');
				return;
			}
			console.log(aqar_author_type_id);
			nafathApi( authorid, aqar_author_type_id );
		}
	});

	$('#nic').on('click', function(e){
		e.preventDefault();
		var nafath_id = $('#nafath_id');
			nafath_id.fadeIn( 300, function() {
				nafath_id.show();
			});
			$('#next-register-btn').fadeIn( 300, function() {
				$('#next-register-btn').show();
			});
      });

		function updateTimer() {
			var timeleft = 180;
			var downloadTimer = setInterval(function(){
			if(timeleft <= 0){
				clearInterval(downloadTimer);
				$('#time-model').fadeOut( 1000, function() {
					$(this).hide();
				});
				clearTimeout(timer);
				document.getElementById("timer").innerHTML = "0";
			} else {
				document.getElementById("timer").innerHTML = timeleft;
			}
			timeleft -= 1;
			}, 1000);
		}

		

		function nafathApi(authorid, aqar_author_type_id) {
			$.ajax({
				type: 'post',
				url: ajax_aqar.ajaxurl,
				dataType: 'json',
				data:{
					action: 'nafathApi',
					id: authorid,
				},	
				beforeSend: function() {
					$('.sync__loader').show();
				},
				complete: function(){
					$('.sync__loader').hide();
				},
				success: function( response ) {
					if( response.success ) {
						var transId	= response.transId;
						$('#id-number > #nafathNumber').empty().html(response.number);
						$('#time-model').fadeIn( 1000, function() {
							$(this).show();
						});
						updateTimer();
						fetchdata(authorid, aqar_author_type_id, transId);
					} else {
						$messages.empty().append('<div dir="ltr" class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.message +'</div>');
						return false;
					}
				},
				error: function(xhr, status, error) {
					var err = eval("(" + xhr.responseText + ")");
					console.log(err.Message);
				}
			});
		}
 
		function fetchdata(authorid, aqar_author_type_id, transId){
			var role = 'houzez_agent';
			 $.ajax({
					type: 'post',
					url: ajax_aqar.ajaxurl,
					dataType: 'json',
					data:{
						action: 'fetchdata',
						authorid: authorid,
						transId: transId,
						aqar_author_type_id: aqar_author_type_id
					},
					success: function(data){
					// Perform operation on return value
					    if( data.success && data.reload ) {
							setTimeout(function(){
								window.location.reload();
					   		}, 5000);
						}
						else if( data.success ){
							screen_1.fadeOut( 200, function() {
								screen_1.hide();
							});
							screen_2.html(data.html);
							$("input[name=full_name]").val(data.arFullName);
							$("input[name=first_name]").val(data.arFirst);
							$("input[name=last_name]").val(data.arGrand);

							if( aqar_author_type_id == '1' ) {
								role  = 'houzez_agent';
							}
							if( aqar_author_type_id == '2' ) {
								role  = 'houzez_agency';
							}
							if( aqar_author_type_id == '3' ) {
								role  = 'houzez_owner';
							}
							if( aqar_author_type_id == '4' ) {
								role  = 'houzez_buyer';
							}
							 
							$("input[name=role]").val(role);
							$("input[name=id_number]").val(data.id);

							
						} else {
							timer = setTimeout(function(){fetchdata(authorid, aqar_author_type_id, transId);}, 1000);
						}
					},
					complete:function(data){
						
					}
				});
			}		

			$('#aq-register-form').submit(function(event) {
				event.preventDefault();
				var currnt = $(this);
				houzez_register( currnt );
			});

			var houzez_register = function ( currnt ) {

				var $form = currnt;
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
		
						currnt.find('.houzez-loader-js').removeClass('loader-show');

						// if(houzez_reCaptcha == 1) {
						// 	$form.find('.g-recaptcha-response').remove();
						// 	if( g_recaptha_version == 'v3' ) {
						// 		houzezReCaptchaLoad();
						// 	} else {
						// 		houzezReCaptchaReset();
						// 	}
						// }
						
					},
					error: function(xhr, status, error) {
						var err = eval("(" + xhr.responseText + ")");
						console.log(err.Message);
					}
				});
			}

	/* ------------------------------------------------------------------------ */
    /* login and register links for elementor button
    /* ------------------------------------------------------------------------ */
	const elements_link = document.querySelector('[data-target="#login-register-form"]  a');
	const element = document.querySelector('[data-target="#login-register-form"]');
	const userID = ajax_aqar.userID;
	const add_listing = ajax_aqar.add_listing;
	if( userID == 0 ) {
		$(elements_link).on('click', function () {
			$('.modal-toggle-1').addClass("active");
			$('.modal-toggle-2').removeClass("active");
			$('.register-form-tab').removeClass("active").removeClass("show");
			$('.login-form-tab').addClass("active").addClass("show");
		});
		$(elements_link).click(function () {
			$('.modal-toggle-2').addClass("active");
			$('.modal-toggle-1').removeClass("active");
			$('.register-form-tab').addClass("active").addClass("show");
			$('.login-form-tab').removeClass("active").removeClass("show");
		});
	} else {
		$(element).attr('data-target', '#');
		$(elements_link).attr('href', add_listing);
	}

});