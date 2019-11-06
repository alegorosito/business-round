jQuery(document).ready(function ($) {
  
  	// Form Validation
	$.validate({
	    modules : 'file',
	    lang: 'es'
	});

	//Table Filter
	$(document).ready(function(){
	  	
		$('#msg-history').scrollTop($('#msg-history').height() + 10000000);
		$('.playerListTitle').hide();
		$('#btnCancelMeeting').hide();
		$('div#formeeting').hide();


		/* Form Perfil */
		
		$('#form-production').on('focus keypress', '.storyline-proyecto', function (e) {


		    var $this = $(this);
		    var msgSpan = $this.parents('#form-production').find('.counter_msg');
		    var ml = parseInt($this.attr('maxlength'), 10);
		    var length = this.value.length;
		    var msg = 'Restan ' + (ml - length) + ' caracteres.';

		    msgSpan.html(msg);
		});

		/* Fin Form Perfil */


		/* PLAYER SELECTION */

		$(".userInfo").click(function() {


			value = $.parseHTML(this.innerHTML);

			var idUser = parseInt(value[3].innerText.trim());
			var userName = value[5].innerText.trim().split("\n")[0].replace('Nombre: ', '');

				wp_ajax_get_user_hs(idUser);

			$('#playerName').text(userName);
			$('#nombre_meeting').val(userName);
			$('#id_acepta').val(idUser);
				
				$('.playerListTitle').show();
			$('.table-front').show();
			$('#btnCancelMeeting').show();

			$('.playerAvailables').hide();
			$('.usersFront').hide();

		});

		/* Tabs Perfil */

		$('#profile-form-img-update').show();
		$('#form-profile').hide();
		$('#form-production').hide();
		
		add_style_tab_selected($('#user-form-tab-profile'));

		$('#user-form-tab-profile').click(function() {
			add_style_tab_selected(this);
			
			remove_style_tab_selected( $('#user-form-tab-personal-data') );
			remove_style_tab_selected( $('#user-form-tab-production') );

			$('#form-profile').hide();
			$('#profile-form-img-update').show();
			$('#form-production').hide();

		});

		$('#user-form-tab-personal-data').click(function() {
			add_style_tab_selected(this);
			
			remove_style_tab_selected( $('#user-form-tab-profile') );
			remove_style_tab_selected( $('#user-form-tab-production') );
			
			$('#form-profile').show();
			$('#profile-form-img-update').hide();
			$('#form-production').hide();
		});

		$('#user-form-tab-production').click(function() {
			add_style_tab_selected(this);
			remove_style_tab_selected( $('#user-form-tab-profile') );
			remove_style_tab_selected( $('#user-form-tab-personal-data') );

			$('#form-profile').hide();
			$('#profile-form-img-update').hide();
			$('#form-production').show();
		});
		
		/* Fin Tabs Perfil */

		
		/* CANCEL */
		$('#btnCancelMeeting').click( function() {
				
				$('.playerListTitle').hide();
				$('#btnCancelMeeting').hide();
				$('.table-front').hide();

				$('.playerAvailables').show();
			$('.usersFront').show();
			$('#formeeting').hide();
		});	

		/* SELECT TIME */
		$('.calendarBtn td a').click(function(a) {
				
				$('.playerListTitle').hide();
				$('#btnCancelMeeting').hide();
				$('.table-front').hide();
				$('.playerAvailables').hide();
			$('div#formeeting').show();

			var hora = $(this).parent().parent().find('td').html();
			hora2 = hora.split(" - ");

			$('#hora_meeting').val(hora);
			$('input[name="hora_meeting"]').val(hora);

		});

	});


		/* ACTUALIZAR CHAT */

		$('.user-contact').click(function() {
			
			var yourtumb = $('#msg-history').find('.your-thumbnail').find('img').attr('src');
			var mythumbnail = $('#msg-history').find('.my-thumbnail').find('img').attr('src');


			if ( yourtumb == null ) {

				yourtumb = '<span class="user-chat-recipe dashicons dashicons-admin-users"></span>';
			
			} else {

				yourtumb = '<img src="' + yourtumb + '" class="img-chat-thumbnail" />';
			
			}


			if ( mythumbnail == null ) {
				
				mythumbnail = '<span class="user-chat-send dashicons dashicons-admin-users"></span>';

			} else {
				
				mythumbnail = '<img src="' + mythumbnail + '" class="img-chat-thumbnail" />';

			}


			$('#msg-history').empty();
			$('.selected-user').removeClass('selected-user');

			var user_name = $(this).find(".user-name").text();
			var id_recibe = this.id.split('_')[1];


			$('#id_recibe').val(id_recibe);
			$('#userid_' + id_recibe).addClass('selected-user');

			var id_envia = $('#id_envia').val(); 
			var id_recibe = $('#id_recibe').val();
			
			var data = {
				'action': 'user_msj',
				'id_envia': id_envia,
				'id_recibe': id_recibe,
			};

			var url = window.location.protocol + "//" + window.location.host + "/" + 'wp-admin/admin-ajax.php';

			jQuery.post(
				url, 
				data, 
				function(res) {
					var obj = JSON.parse(res);
			        
			        $.each(obj, function(key,val){
				        var msj = '';

			            if (parseInt(obj[key].id_envia) == data.id_envia) {

							msj += '<div class="my-msg">';
							msj += 		'<div class="my-content">T&uacute: ';
							msj +=			'<div class="my-thumbnail">' + mythumbnail + '</div>';
							msj += 			'<div class="my-message">';
							msj += obj[key].mensaje;
							msj += '</div></div></div>';
							msj += '<div class="timestamp_my_message"><p>' + obj[key].fecha_hora + '</p></div>';

							$('#msg-history').append(msj);

			            } else {

							msj += '<div class="your-msg">';
							msj += 		'<div class="your-content">'+ user_name.trim() + ': ';
							msj +=			'<div class="your-thumbnail">' + yourtumb + '</div>';
							msj += 			'<div class="your-message">';
							msj += obj[key].mensaje;
							msj += '</div></div></div>';
							msj += '<div class="timestamp_your_message"><p>' + obj[key].fecha_hora + '</p></div>';

							$('#msg-history').append(msj);

			            }

						$('#msg-history').scrollTop($('#msg-history').height() + 10000000);

			        });
					
			});


	});

	function wp_ajax_get_user_hs(idUser) {
			var data = {
				'action': 'user_hs',
				'id': idUser,
			};

			var url = window.location.protocol + "//" + window.location.host + "/" + 'wp-admin/admin-ajax.php';
			jQuery.post(
				url, 
				data, 
				function(res) {
					
					var obj = JSON.parse(res);
			        $.each(obj, function(key,val){
			            var userhs = val.fecha_hora.split(" ");
			            userhs = userhs[1].split(":");
			            userhs = userhs[0] + userhs[1];

			            $('#' + userhs + ' .' + 'btn').html('No disponible');
			        });
			});

	}

	function add_style_tab_selected(tab){
		$(tab).css('background-color','#32373c');
		$(tab).css('color','white');
	}
	
	function remove_style_tab_selected(tab){
		$(tab).css('background-color','white');
		$(tab).css('color','#7b7b7b');
	}

});

