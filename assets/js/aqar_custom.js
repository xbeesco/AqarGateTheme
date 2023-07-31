jQuery(document).ready(function($){	

	var screen_1 = $('#register-screen-1');
	var screen_2 = $('#register-screen-2');

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
		if( aqar_author_type_id === '1' ||  aqar_author_type_id === '2') {
			if(  authorid== null ||  authorid == ""  ) {
				alert('ادخل رقم الهوية');
				return;
			}
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
			var timeleft = 60;
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

		var $messages = $('#hz-register-messages');

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
						fetchdata(authorid, aqar_author_type_id, transId)
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
						transId: transId
					},
					success: function(data){
					// Perform operation on return value
						if( data.success ){
							$("input[name=full_name]").val(data.arFullName);
							$("input[name=first_name]").val(data.arFirst);
							$("input[name=last_name]").val(data.arGrand);

							if( aqar_author_type_id == '1' ) {
								role  = 'houzez_agent';
							}
							if( aqar_author_type_id == '2' ) {
								role = 'houzez_agency';
							}
							$("input[name=role]").val(role);
							$("input[name=id_number]").val(data.id);

							screen_1.fadeOut( 200, function() {
								screen_1.hide();
							});
							screen_2.fadeIn( 1000, function() {
								screen_2.show();
							});
						} else {
							timer = setTimeout(function(){fetchdata(authorid, aqar_author_type_id, transId);}, 1000);
						}
					},
					complete:function(data){
						
					}
				});
			}		
});