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

	/* ------------------------------------------------------------------------ */
        /*  Property additional Features
         /* ------------------------------------------------------------------------ */
		 $( "#rer-borders" ).sortable({
            revert: 100,
            placeholder: "detail-placeholder",
            handle: ".sort-additional-row",
            cursor: "move"
        });

        $( '.add-additional-row' ).click(function( e ){
            e.preventDefault();

            var numVal = $(this).data("increment") + 1;
            $(this).data('increment', numVal);
            $(this).attr({
                "data-increment" : numVal
            });

            var newAdditionalDetail = '<tr>'+
                '<td class="table-q-width">'+
                '<input class="form-control" type="text" name="rerBorders['+numVal+'][direction]" id="direction_'+numVal+'" value="">'+
                '</td>'+
                '<td class="table-q-width">'+
                '<input class="form-control" type="text" name="rerBorders['+numVal+'][type]" id="type_'+numVal+'" value="">'+
                '</td>'+
				'<td class="table-q-width">'+
                '<input class="form-control" type="text" name="rerBorders['+numVal+'][length]" id="length_'+numVal+'" value="">'+
                '</td>'+
                '<td class="">'+
                '<a class="sort-additional-row btn btn-light-grey-outlined"><i class="houzez-icon icon-navigation-menu"></i></a>'+
                '</td>'+
                '<td>'+
                '<button data-remove="'+numVal+'" class="remove-additional-row btn btn-light-grey-outlined"><i class="houzez-icon icon-close"></i></button>'+
                '</td>'+
                '</tr>';

            $( '#rer-borders').append( newAdditionalDetail );
            removeAdditionalDetails();
        });

        var removeAdditionalDetails = function (){

            $( '.remove-additional-row').click(function( event ){
                event.preventDefault();
                var $this = $( this );
                $this.closest( 'tr' ).remove();
            });
        }
        removeAdditionalDetails();


		var processing_text = houzezProperty.processing_text;
		var are_you_sure_text = houzezProperty.are_you_sure_text;
		/*--------------------------------------------------------------------------
         *  update property
         * -------------------------------------------------------------------------*/
        $( 'a.update-property' ).on( 'click', function (){
            
			var $this = $( this );
			var propID = $this.data('id');
			var propNonce = $this.data('nonce');
			var editLink = $this.data('edit');

			bootbox.confirm({
			title: "سبب التعديل ?",
			message: '<select class="form-control" id="update-notes"><option value="newMarketing">تسويق جديد</option><option value="Other">اخري</option></select>',
			buttons: {
				confirm: {
				label: 'موافقة',
				className: 'btn-primary'
				},
				cancel: {
				label: 'الغاء',
				className: 'btn-secondary'
				}
			},
			callback: function (result) {
				if(result==true) {
					var processing_text = 'جاري التحويل الي صفحة تعديل الاعلان';
					fave_processing_modal( processing_text );
					// User clicked the confirm button
					var textareaValue = document.getElementById('update-notes').value;
					if (textareaValue.trim() !== '') {
					  console.log('Textarea value:', textareaValue);
					  // Perform further actions with the textarea value
					} else {
					  // Textarea is empty, display an error message or take appropriate action
					  	jQuery('#fave_modal').modal('hide');
					  	alert( 'من فضلك اكتب سبب التعديل' );
						return false;
					}
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: ajax_aqar.ajaxurl,
						data: {
							'action': 'aqar_update_property',
							'prop_id': propID,
							'security': propNonce,
							'content': textareaValue,
							'edit_link': editLink
						},
						success: function(data) {
							if ( data.success == true ) {
								window.location = data.redirect;
							} else {
								jQuery('#fave_modal').modal('hide');
								alert( data.reason );
							}
						},
						error: function(errorThrown) {

						}
					}); // $.ajax
				} // result
			} // Callback
		});

		return false;
		
		});

		/*--------------------------------------------------------------------------
         *  cancel property
         * -------------------------------------------------------------------------*/
        $( 'a.cancel-property' ).on( 'click', function (){
            
			var $this = $( this );
			var propID = $this.data('id');
			var propNonce = $this.data('nonce');
			var editLink = $this.data('edit');

			bootbox.confirm({
			title: "سبب الالغاء ?",
			message: '<select class="form-control" id="cancel-notes"><option value="SoldProperty">تم بيع العقار</option><option value="RentedProperty">تم تأجير العقار</option><option value="TransferredProperty">تم نقل ملكية العقار</option><option value="IncorrectAdvertising">بيانات ترخيص الإعلان العقاري غير صحيحة</option><option value="Other">اخري</option></select>',
			buttons: {
				confirm: {
				label: 'موافقة',
				className: 'btn-primary'
				},
				cancel: {
				label: 'الغاء',
				className: 'btn-secondary'
				}
			},
			callback: function (result) {
				if(result==true) {
					// User clicked the confirm button
					var textareaValue = document.getElementById('cancel-notes').value;
					if (textareaValue.trim() !== '') {
					  console.log('Textarea value:', textareaValue);
					  // Perform further actions with the textarea value
					} else {
					  // Textarea is empty, display an error message or take appropriate action
					  	jQuery('#fave_modal').modal('hide');
					  	alert( 'من فضلك اكتب سبب الالغاء .' );
						return false;
					}
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: ajax_aqar.ajaxurl,
						data: {
							'action': 'aqar_cancel_property',
							'prop_id': propID,
							'security': propNonce,
							'content': textareaValue,
							'edit_link': editLink
						},
						beforeSend: function( ) {
							houzez_processing_modal(processing_text);
						},
						success: function(data) {
							if ( data.success == true ) {
								var response = '<br><strong>'+data.reason+'</strong>';
								jQuery('#fave_modal .houzez_messages_modal').empty();
								jQuery('#fave_modal .houzez_messages_modal').html(response);
								setTimeout(function() {
									window.location.reload()
								}, 1000);
							} else {
								var response = '<br><strong>'+data.reason+'</strong>';
								jQuery('#fave_modal .houzez_messages_modal').empty();
								jQuery('#fave_modal .houzez_messages_modal').html(response);
							}
						},
						error: function(errorThrown) {

						}
					}); // $.ajax
				} // result
			} // Callback
		});

		return false;
		
		});


		/*--------------------------------------------------------------------------
         *  draft property
         * -------------------------------------------------------------------------*/
        $( '.draft-property' ).on( 'click', function( e ) {
            e.preventDefault();
            var $this = $( this );
            var propid = $this.data( 'property' );
            $.ajax({
                url: ajax_aqar.ajaxurl,
                data: {
                    'action': 'aqargate_property_draft',
                    'propID': propid
                },
                method: 'POST',
                dataType: "JSON",

                beforeSend: function( ) {
                    houzez_processing_modal(processing_text);
                },
                success: function( response ) {
                    window.location.reload();
                },
                complete: function(){
                }
            });

        });

		/*--------------------------------------------------------------------------
         *  edit property
         * -------------------------------------------------------------------------*/
        $( '#edit-property' ).on( 'click', function( e ) {
            e.preventDefault();
			var $this    = $( this );
			var propID   = $this.data('property');
			var form     = $("#submit_property_form");
			var editLink = $this.data('edit');

			bootbox.confirm({
				message: "<p><strong>"+are_you_sure_text+"</strong></p>",
				buttons: {
					confirm: {
					label: 'موافقة',
					className: 'btn-primary'
					},
					cancel: {
					label: 'الغاء',
					className: 'btn-secondary'
					}
				},
				callback: function (result) {
					if(result==true) {
					// User clicked the confirm button
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: ajax_aqar.ajaxurl,
						data: {
							'action': 'aqargate_edit_api_property',
							'propID': propID,
							'edit_link': editLink,
							'formData': form.serialize()
						},
						beforeSend: function( ) {
							houzez_processing_modal(processing_text);
							jQuery('#fave_modal .houzez_messages_modal').empty();
							var response = '<br><strong>'+processing_text+'</strong>';
							jQuery('#fave_modal .houzez_messages_modal').html(response);
						},
						success: function(data) {
							if ( data.success == true ) {
								var response = '<br><strong>'+data.reason+'</strong>';
								jQuery('#fave_modal .houzez_messages_modal').empty();
								jQuery('#fave_modal .houzez_messages_modal').html(response);
								// Delay form submission
								setTimeout(function() {
									form.submit();  // Submit after 2000ms (2 seconds)
								}, 1000);
							} else {
								var response = '<br><strong>'+data.reason+'</strong>';
								jQuery('#fave_modal .houzez_messages_modal').empty();
								jQuery('#fave_modal .houzez_messages_modal').html(response);
								// jQuery('#fave_modal').modal('hide');
								// alert( data.reason );
							}
						},
						error: function(errorThrown) {

						}
					}); // $.ajax
				} // result
			} // Callback
		});

		return false;

        });

		var houzez_processing_modal = function ( msg ) {
            var process_modal ='<div class="modal fade" id="fave_modal" tabindex="-1" role="dialog" aria-labelledby="faveModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body houzez_messages_modal">'+msg+'</div></div></div></div></div>';
            jQuery('body').append(process_modal);
            jQuery('#fave_modal').modal();
        };

});