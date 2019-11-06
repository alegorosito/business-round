jQuery(document).ready(function ($) {
	
	//Table Filter
	$(document).ready(function(){
	
	  $("#userFilter").on("keyup", function() {
	    var value = $(this).val().toLowerCase();
	    $("#br-users-table tr").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	    });
	  });

	  $("#meetingFilter").on("keyup", function() {
	    var value = $(this).val().toLowerCase();
	    $("#br-meetings-table tr").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	    });
	  });
	
	});
	
	// Form Validation
	$.validate({
	    lang: 'es'
	});

	$('.btn-delete-user').click(function(e){
		e.preventDefault();

		var partic = this.id;
		var p = partic.split("-");

		var r = confirm("¿Seguro que desea eliminar al participante?");

		if (r == true) {
			
			var id_user = p[2];

			var data = {
				'action': 'del_user',
				'id': id_user,
			};

			var url = window.location.protocol + "//" + window.location.host + "/" + 'wp-admin/admin-ajax.php';

			jQuery.post(
				url, 
				data, 
				function(res) {
					
			  	  $('#row-user-' + res).remove();
			        
			});
		
		} 

	});

	$('select#tipo_participante').on('change', function() {

		if (this.value == 'Productor') {
			$('#lblEmpresa1').text('Proyecto');
			$('#lblEmpresa2').text('Proyecto');
		} else {
			$('#lblEmpresa1').text('Empresa');
			$('#lblEmpresa2').text('Empresa');
		}

	});

	$('#btnsave').attr("disabled", true);
	$('#userForm').hide();

	$('#show-pass-user').click(function(e) {
		e.preventDefault();
		$('#password-user').attr("type", "text");
	});

	$('#show-pass-user2').click(function(e) {
		e.preventDefault();
		$('#password-user').attr("type", "text");
	});

	$('.btnuser').click( function() {

		$('#userForm').show();
		$('.list-br-users').hide();
		$('.container').css('height','1300px');
				
		$('#br-users tr').click(function (a, b) {

                var id_wp_user = $(this).closest("tr").find('.attrID', b).text();
                var Nombre = $(this).closest("tr").find('.attrName', b).text();
                var Apellido = $(this).closest("tr").find('.attrLastname', b).text();
                var Tipo_participante = $(this).closest("tr").find('.attrTipo', b).text();
                var Empresa = $(this).closest("tr").find('.attrEmpresa', b).text();
                var Tel = $(this).closest("tr").find('.attrTel', b).text();
                var Ciudad = $(this).closest("tr").find('.attrCiudad', b).text();
                var Provincia = $(this).closest("tr").find('.attrProvincia', b).text();
                var Dni = $(this).closest("tr").find('.attrDni', b).text();
                var Repa = $(this).closest("tr").find('.attrRepa', b).text();
                var Email = $(this).closest("tr").find('.attrEmail', b).text();
                var Rol = $(this).closest("tr").find('.attrRol', b).text();
                var Proyecto = $(this).closest("tr").find('.attrProyecto', b).text();

                $('#id_wp_user').val(id_wp_user.trim());
                $('#nombre').val(Nombre.trim());
				$('#apellido').val(Apellido.trim());
				$('#tipo_participante').val(Tipo_participante.trim());
				$('#empresa').val(Empresa.trim());
				$('#telefono').val(Tel.trim());
				$('#ciudad').val(Ciudad.trim());
				$('#provincia').val(Provincia.trim());
				$('#dni').val(Dni.trim());
				$('#repa').val(Repa.trim());
				$('#email').val(Email.trim());
				$('#rol').val(Rol.trim());
				$('#proyecto').val(Proyecto.trim());


				if (Tipo_participante.trim() == 'Productor') {
					$('#lblEmpresa1').text('Proyecto');
					$('#lblEmpresa2').text('Proyecto');
				} else {
					$('#lblEmpresa1').text('Empresa');
					$('#lblEmpresa2').text('Empresa');
				}


				$('#btnsave').attr("disabled", false);
               
            });

		

	});

	/* Meeting List */
	$('.btn-delete-meeting').click(function(e) {

		var partic = this.id;
		var p = partic.split("-");

		var r = confirm("¿Seguro que desea eliminar la reunión entre los participantes " + p[2] + " y " + p[3] + "?");

		if (r == true) {
			
			var id_meeting = this.id;

			var data = {
				'action': 'del_meeting',
				'id': id_meeting,
			};

			var url = window.location.protocol + "//" + window.location.host + "/" + 'wp-admin/admin-ajax.php';

			jQuery.post(
				url, 
				data, 
				function(res) {
					
			  	  $('#row-meeting-' + res).remove();
			        
			});
		
		} 

	});


	/* New User Form */

	$('#nuevo-user-btn').click(function() {
		$('#list-users-table').hide();
		$('#newUserForm').show();
	});


	$('#gen-pass-user').click(function(e) {
		e.preventDefault();
		var pass = make_pass(10);
		alert('Copie la contraseña a continuación: ' + pass)
		$('#password-user').val(pass);
	});

	$('#gen-pass-user2').click(function(e) {
		e.preventDefault();
		var pass = make_pass(10);
		alert('Copie la contraseña a continuación: ' + pass)
		$('#password-user2').val(pass);
	});

	function make_pass(length) {
	   var result           = '';
	   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	   var charactersLength = characters.length;
	   for ( var i = 0; i < length; i++ ) {
	      result += characters.charAt(Math.floor(Math.random() * charactersLength));
	   }
	   return result;
	}

});
