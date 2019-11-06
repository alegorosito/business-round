<?php 
	/**
	 * 
	 */
	class FrontUserDashboard
	{
		
		public static function dashboard()
		{

			// Display the User Dashboard
			$usermeetings = new BRCalendar;
			$users = new BRUser;
			$userdata = wp_get_current_user();

			$dashboard_data = "";
			

			if (is_user_logged_in())
			{

				if ($_POST) {

					if (isset($_POST['meeting-response'])) {
						
						$post = new BRCleanPostData;					

						$resultado = $post->cleanPost($_POST['meeting-response']);
						$idMeeting = $post->cleanPost($_POST['id-meeting']);
						$horaMeeting = $post->cleanPost($_POST['hora-meeting']);

						if ($resultado == 'Aceptar') {
							$usermeetings->updateMeeting($idMeeting, $horaMeeting);
						} elseif ($resultado == 'Rechazar') {
							$usermeetings->removeMeeting($idMeeting);
						}

					}
					

				}
				
				$dashboard_data = '
					<div class="dashboard-info">
						<div class="user-info"><h3>Reuniones para '. $userdata->user_firstname .' '. $userdata->user_lastname . '</h3></div>
						<div class="pull-right"><a href="http://' . $_SERVER['HTTP_HOST'] . '/agendar/">Agendar nueva reuni&oacuten <span class="dashicons dashicons-calendar-alt"></span></a></div>
					</div><br>';

				/*
					Agenda
				*/

				$reuniones = $usermeetings->userAcceptedMeetings($userdata->ID);
				$trAceptados = "";

				foreach ($reuniones as $reunion) {
				
					$horario = explode(" ", $reunion->fecha_hora)[1];
					$horario = explode(":", $horario);
					$horario = $horario[0] . ":" . $horario[1];

					$invitado = '';
					
					if (intval($reunion->id_acepta) == $userdata->ID) {
						$invitado_reunion = $reunion->nombre_invita . ' '. $reunion->apellido_invita;
						$user_chat_id = $reunion->id_solicita;
						$invitado = $users->getUser($reunion->id_solicita)[0];
					} else {
						$invitado_reunion = $reunion->nombre_acepta . ' '. $reunion->apellido_acepta;
						$user_chat_id = $reunion->id_acepta;
						$invitado = $users->getUser($reunion->id_acepta)[0];
					}

					if (!$reunion->aceptado) {
						$trClase = "Pendiente";
						$btnMsj = '';
					} else {
						$trClase = "Aceptado";
						$btnMsj = '<a href="http://' . $_SERVER['HTTP_HOST'] .'/chat?user=' . $user_chat_id . '" class="button button-primary br-button-chat" title="Enviar un mensaje al usuario">Chat <span class="dashicons dashicons-admin-comments" ></span></i>
		                        </a>';
					}

					$thumb = '';
					if ( isset($userThumb->user_thumbnail) && $invitado->user_thumbnail !== '') {
						$thumb ='<img src="' .$invitado->user_thumbnail. '" class="img-list-thumbnail" />';
					} 

					$trAceptados .= '<tr class="'. $trClase .'">
							<td>  
								' . $thumb . '

									<p id="user-name-list">' . $invitado_reunion . '</p>';
					if ( isset($invitado->proyecto) && $invitado->proyecto != '') {
						$trAceptados .= '<a href="' . $invitado->proyecto . '" title="Ir al Proyecto del usuario." target="_blank">
											<span class="user-project dashicons dashicons-media-interactive"></span></i>
			                        	</a>';
					}

					$trAceptados .='</td>
							<td class="center-data">
								' . '07-11-2019' . '
							</td>
							<td class="center-data">
								' . $horario . '
							</td>
							<td class="center-data">
								' . $trClase . '
							</td>
							<td class="center-data">
								'. $btnMsj .'
							</td>
						</tr>';
				}

				if (count($reuniones) > 0) {
					$dashboard_data .= '
						<div class="dashboard-info">
							<h4>Agenda</h4>
							<table id="table-front" >
								<thead>
									<tr >
										<td>
											Reuni&oacuten con 
										</td>
										<td  class="center-data">
											D&iacutea 
										</td>
										<td  class="center-data">
											Horario
										</td>
										<td class="center-data">
											Estado
										</td>
										<td class="center-data">
											Enviar Mensaje
										</td>
									</tr>
								</thead>
								<tbody>'.
									$trAceptados
								.'</tbody>
							</table>
						</div><br>
						';
				} else {
					$dashboard_data .= '
						<div class="dashboard-info">
							<h4>Agenda</h4>
							<div><strong>A&uacuten no tiene reuniones programadas.</strong></div>
						</div><br>
						';
				}

				$Pendientes = $usermeetings->userPendingMeetings($userdata->ID);
				$trPendientes = "";

				foreach ($Pendientes as $reunion) {
					$horario = explode(" ", $reunion->fecha_hora)[1];
					$horario = explode(":", $horario);
					$horario = $horario[0] . ":" . $horario[1];

					$formAcept = '
						<form method="post">
							<input type="hidden" name="hora-meeting" value="' . $reunion->fecha_hora . '">
							<input type="hidden" name="id-meeting" value="' . $reunion->id . '">
							<input type="submit" class="button-accept" name="meeting-response" value="Aceptar"></input>
						</form>
					';

					$formCancel = '
						<form method="post">
							<input type="hidden" name="hora-meeting" value="' . $reunion->fecha_hora . '">
							<input type="hidden" name="id-meeting" value="' . $reunion->id . '">
							<input type="submit" class="button-cancel" name="meeting-response" value="Rechazar"></input>
						</form>
					';
						


					$trPendientes .= '<tr>
							<td class="center-vertical-td">
								' . $reunion->nombre_solicita . ' ' . $reunion->apellido_solicita . '';

					$usersP = new BRUser;
					$userPend = $usersP->getUser($reunion->id_solicita)[0];

					if ( isset($userPend->proyecto) && $userPend->proyecto != '') {
						$trPendientes .= '<a href="' . $userPend->proyecto . '" title="Ir al Proyecto del usuario." target="_blank">
											<span class="user-project dashicons dashicons-media-interactive"></span></i>
			                        	</a>';
					}

					$trPendientes .= '</td>
							<td class="center-data center-vertical-td">
								' . 'Proyecto' . '
							</td>
							<td class="center-data center-vertical-td"">
								' . 'Tipo' . '
							</td><td class="center-data center-vertical-td">
								' . '07-11-2019' . '
							</td>
							<td class="center-data center-vertical-td">
								' . $horario . '
							</td>
							<td class="center-data  center-vertical-td">
								'. $formAcept .'
								'. $formCancel .'
							</td>
						</tr>';
				}



				/*
					Solicitudes
				*/

				if (count($Pendientes) > 0) {
					$dashboard_data .= '
						<div class="dashboard-info">
							<h4>Solicitudes Pendientes</h4>
							<table id="table-front">
								<thead>
									<tr>
										<td>
											Solicita 
										</td>
										<td class="center-data">
											Proyecto 
										</td>
										<td class="center-data">
											Tipo 
										</td>
										<td class="center-data">
											D&iacutea 
										</td>
										<td class="center-data">
											Horario
										</td>
										<td class="center-data">
											Acciones
										</td>
									</tr>
								</thead>
								<tbody>'. 
									$trPendientes
								 .'</tbody>
							</table>
						</div>
						';
				} else {
					$dashboard_data .= '
						<div class="dashboard-info">
							<h4>Solicitudes Pendientes</h4>
							<div><strong>A&uacuten no ha recibido solicitudes de otros participantes.</strong></div>
						</div>
						';
				}



			} else {
				$dashboard_data .= '<h3>Debe iniciar sesi&oacuten para continuar</h3>
					<a href="' . wp_login_url( get_permalink() ) . '" title="Login">Iniciar Sesi&oacuten</a>
				';
			}

			return $dashboard_data;
		}

		public static function calendar()
		{
			
			$calendar = new BRCalendar;
			$userSolicita = new BRUser;
			$userAcepta = new BRUser;

			$user = wp_get_current_user();


			$horas1 = ["1400","1415","1430","1445","1500","1515","1530","1545"];

			$horas2 = ["1600","1615","1630","1645","1700","1715","1730","1745"];

			$html = '
					<div style="display: none;" class="playerListTitle" >
						<h3>Agendar una reuni&oacuten con <strong id="playerName"></strong></h3>
						<h4>Para continuar seleccione una de las horas disponibles: </h4>
						<table id="exampleTable" style="width:500px;">
							<tr>
								<td>Disponible</td>
								<td class="calendarBtn">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							</tr>
							<tr>
								<td>En espera de aceptaci&oacuten</td>
								<td class="enEspera"></td>
							</tr>
							<tr>
								<td>Reuni&oacuten aceptada</td>
								<td class="agendado"></td>
							</tr>
						</table>
					</div>
					<br>
					<div id="meetings" >
					';

			$rows = '';

			foreach ($horas1 as $key => $hora) {
								
				$horaA = substr($hora, 0,2) . ":" . substr($hora,2,4);
				$intervalo = $calendar->getInterval($horaA, $user->ID);

				$iTiempo1 = "";

				$hs = substr($hora, 0,2);
				$mins = substr($hora, 2,2);
				$iTiempo1 .=  $hs. ':'. $mins;

				if (intval($mins) == 45) {
					$hasta =  (intval($hs) + 1) . ':00';
				} else $hasta = $hs. ':'. ($mins + 15);

				$iTiempo1 .= ' - ' . $hasta . '</td>';


				if (!isset($intervalo[0]->aceptado)) {
				
					$rows .=	'<tr id="' . $hora .	'" class="calendarTime calendarBtn">
									<td >' .$iTiempo1. '</td>
									<td class="btn">
										<a>Agendar Reunion</a>
									</td>
								</tr>';

				} else {
					
					$userA = (intval($intervalo[0]->id_acepta) == $user->ID) ? $userAcepta->getUser($intervalo[0]->id_solicita) : $userAcepta->getUser($intervalo[0]->id_acepta);

					$claseAceptado = ($intervalo[0]->aceptado ? "agendado" : "enEspera");

					$rows .=	'<tr id="' . $hora .	'" class="calendarTime">
									<td >' .$iTiempo1. '</td>
									<td class="'. $claseAceptado .'">' .
									$userA[0]->first_name ." " . $userA[0]->last_name
								 . '</td>
								 </tr>';

				}
						

			}

			$table = ' 
				<table class="table-front" style="display: none;">
					<thead>
						<tr>
							<td >Hora</td>
							<td >Jueves 07 de Nov.</td>
						</tr>
					</thead>
					<tbody>'; 
						
			$table .= $rows;	
											
			$table .= '</tbody>
						</table>
					
					';

			$rows2 = '';

			foreach ($horas2 as $key => $hora2) {
								
				$hora2A = substr($hora2, 0,2) . ":" . substr($hora2,2,4);
				$intervalo2 = $calendar->getInterval($hora2A, $user->ID);

				$iTiempo2 = "";

				$hs = substr($hora2, 0,2);
				$mins = substr($hora2, 2,2);
				$iTiempo2 .=  $hs. ':'. $mins;
				

				if (intval($mins) == 45) {
					$hasta =  (intval($hs) + 1) . ':00';
				} else $hasta = $hs. ':'. ($mins + 15);

				$iTiempo2 .= ' - ' . $hasta . '';

				if (!isset($intervalo2[0]->aceptado)) {
				
					$rows2 .=	'<tr id="' . $hora2 .	'" class="calendarTime calendarBtn">
									<td >' .$iTiempo2. '</td>
									<td class=" btn">
										<a>Agendar Reunion</a>
									</td>
								</tr>';

				} else {
					
					$userA = $intervalo2[0]->id_acepta == $user->ID ? $userAcepta->getUser($intervalo2[0]->id_solicita) : $userAcepta->getUser($intervalo2[0]->id_acepta);

					$claseAceptado = ($intervalo2[0]->aceptado ? "agendado" : "enEspera");

					$rows2 .=	'<tr id="' . $hora2 .	'" class="calendarTime">
									<td >' .$iTiempo2. '</td>
									<td class="'. $claseAceptado .'">' .
									$userA[0]->first_name ." " . $userA[0]->last_name
								 . '</td>
								 </tr>';

				}
						

			}

			$table2 = ' 
				<table class="table-front" style="display: none;">
					<thead>
						<tr>
							<td >Hora</td>
							<td >Jueves 07 de Nov.</td>
						</tr>
					</thead>
					<tbody>'; 
						
			$table2 .= $rows2;	
											
			$table2 .= '</tbody>
						</table>
					
					';

			$html .= $table . $table2;
			$html .= '</div><br>
					<a href="#" class="button button-cancel" id="btnCancelMeeting">Cancelar</a><br>';

			return $html;
		}


		public static function playerList() 
		{

			if (isset($_POST)) {

				if (isset($_POST['hora_meeting']) && $_POST['action'] == 'create_meeting') {
						
					$brcalendar = new BRCalendar;
					$post = new BRCleanPostData;					

					$time = $post->cleanPost($_POST['hora_meeting']);
					$id_solicita = $post->cleanPost($_POST['id_solicita']);
					$id_acepta = $post->cleanPost($_POST['id_acepta']);
					
					$brcalendar->createMeeting($time, $id_solicita, $id_acepta);

					 echo "<script type='text/javascript'>
					        window.location=document.location.href;
					        </script>";
				}
			}

			$brUsers = new BRUser;
			
			if (!(is_user_logged_in())) {
				$playersList = '<h3>Debes iniciar sesi&oacute para continuar</h3>
					<a href="' . wp_login_url( get_permalink() ) . '" title="Login">Iniciar Sesi&oacuten</a>
				';
			} else {


			$playersList = '<h3 class="playerAvailables">Participantes disponibles</h3>
				<div class="usersFront">';

				$userId = wp_get_current_user()->ID;
				
				$availableUsers = $brUsers->getUsersAvailables($userId);

				if (count($availableUsers) > 0) {
					foreach ($availableUsers as $user) {

						if (!isset($user->user_thumbnail) || ($user->user_thumbnail == '')) {
							$user_img = '<div class="user-no-thumbnail-available"><span class="dashicons dashicons-nametag"></span></div>';
						} else {
							$user_img = '<img class="user-no-thumbnail-available" src="' . $user->user_thumbnail . '">';
						}

						if ($user->tipo_participante == 'Productor') {
							$tipo_participante_div = '<div class="Empresa">Proyecto: ' . $user->empresa . '</div>';
						} else {
							$tipo_participante_div = '<div class="Empresa">Empresa: ' . $user->empresa . '</div>';
						}

						$playersList .= '
							<div class="userInfo">
									'. $user_img .'
								<div id="userid" style="display: none;">' . $user->id_wp_user . '</div>
								<div class="userDescription">
									<div class="userName"> Nombre: ' . $user->first_name . ' ' . $user->last_name . '</div>
										' . $tipo_participante_div . '			
								</div>
							</div> ';

					} 
				} else {
					$playersList .= '<p>No quedan participantes por agendar. <a href="/panel-de-usuario">Volver al Panel de Usuario.</a></p>';
				}

			
			}
					
			$playersList .= '</div>';
			
			return $playersList;
		}


		public static function meetingForm() 
		{
			
			$html = '
				<div class="form-style" id="formeeting">
					<h2 class="h2-form">Solicitar Reuni&oacuten: &nbsp;&nbsp;Jueves 07 de Nov.</h2>
					<form method="post" id="meetingForm" >
						<input type="hidden" name="action" value="create_meeting" >
						<input type="hidden" name="id_solicita"  id="id_solicita" value="' . wp_get_current_user()->ID . '" >
						<input type="hidden" name="id_acepta"  id="id_acepta" >
						<label for="nombre_meeting" >Player: </label>
						<input id="nombre_meeting" name="nombre_meeting" type="text" readonly >
						<br>
						<label for="hora_meeting">Horario: </label>
						<input id="hora_meeting" name="hora_meeting" type="text" readonly >
						<br>
						<input id="btnsave" type="submit" >
					<form>
				</div>
			';
			
			return $html;
		}

		public static function wpChat() 
		{
			
			if (is_user_logged_in()) {
				
				$brChat = new BRChat;
				$brUsers = new BRUser;

				$firstUserId = null;

				if (isset($_POST)) {

					if (isset($_POST['id_envia']) && isset($_POST['id_recibe']) && isset($_POST['mensaje'])) {

						$post = new BRCleanPostData;

						$id_envia = $post->cleanPost($_POST['id_envia']); 
						$id_recibe = $post->cleanPost($_POST['id_recibe']); 
						$mensaje = $post->cleanPost($_POST['mensaje']);

						$brChat->sendMessage($id_envia, $id_recibe, $mensaje);

						$brChat->setLastChatUserID($id_envia, $id_recibe);

						echo "<script type='text/javascript'>
							        window.location=document.location.href;
								</script>";

					}

				}

				$userSel = '';

				if ( isset($_GET) ) {
					if ( isset($_GET['user']) ) {
						$firstUserId = intval( $_GET['user'] );
					}
				}

				$users = $brChat->getAvailableChats(wp_get_current_user()->ID);

				
				/* Lista de usuarios */
				$userList = '';

				$firstUserId = (isset($firstUserId)) ? $firstUserId : $brChat->getLastChatUserID(wp_get_current_user()->ID);

				if (!isset($firstUserId)) {

					if (isset($users[0]->id_acepta)) {

						$firstUserId = $users[0]->id_acepta;

					} else {

						$html =' <div>
									A&uacuten no tienes reuniones agendadas. Para realizar solicitudes a otros participantes dir&iacutegete a tu <a href="/panel-de-usuario">Panel de Usuario</a>.
								<div> ';
						return $html;

						wp_die();
					}
				}
				
				$nombre_user_chat = '';

				$usersThumb = new BRUser;
				$thumb = '';

				foreach ($users as $user) {


					if ($userSel !== '') {

						$selected = (intval($user->id_acepta) == intval($userSel)) ? "selected-user" : "";

					} else {

						$selected = (intval($user->id_acepta) == intval($firstUserId)) ? "selected-user" : "";

					}

					//$selected = ($user->id_acepta == $firstUserId) ? "selected-user" : "";

					if ($user->id_acepta == wp_get_current_user()->ID) {

						$nombre_user_chat = $user->nombre_invita . ' ' . $user->apellido_invita;
						$id_user_chat = $user->id_solicita;

						$userThumb = $usersThumb->getUser($user->id_solicita)[0];
						if ( isset($userThumb->user_thumbnail) &&  $userThumb->user_thumbnail !== '') {

							$thumb = '<img src="'. $userThumb->user_thumbnail .'" class="img-list-thumbnail" />';
						}

					} else {
						$nombre_user_chat = $user->nombre_acepta . ' ' . $user->apellido_acepta;
						$id_user_chat = $user->id_acepta;

						$userThumb = $usersThumb->getUser($user->id_acepta);

						if ( isset($userThumb->user_thumbnail) &&  $userThumb->user_thumbnail !== '') {
							$thumb = '<img src="'. $userThumb->user_thumbnail .'" class="img-list-thumbnail" />';
						}

					}

					$userList .= '
						<div class="user-contact ' . $selected . '" id="userid_' . $id_user_chat . '">
							<div class="user-thumbnail">
								<div class="user-img">
									'. $thumb .'
								</div>
							</div>
							<div class="user-name">
								'. $nombre_user_chat .'
							</div>
						</div>
					';

				}



				/* Mensajes */
				$mensajes = $brChat->getMessages(wp_get_current_user()->ID, $firstUserId);

				$msj = '';

				foreach ($mensajes as $mensaje) {

					$fecha_hora = explode(" ", $mensaje->fecha_hora);
					$fecha = explode("-", $fecha_hora[0]);
					$fecha = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];
					$hora = explode(":", $fecha_hora[1]);
					$fecha_hora = $fecha . " " . $hora[0] . ":" . $hora[1];
					
					if ($mensaje->id_envia == wp_get_current_user()->ID) {
						
						$thumb = "";
						$user_recibe = $brUsers->getUser($mensaje->id_recibe);
						
						$userThumb = $usersThumb->getUser(wp_get_current_user()->ID)[0];

						if ( isset($userThumb->user_thumbnail) &&  $userThumb->user_thumbnail !== '') {

							$thumb = '<img src="'. $userThumb->user_thumbnail .'" class="img-chat-thumbnail" />';
						} else {
							$thumb = '<span class="user-chat-send dashicons dashicons-admin-users"></span>';
						}

						$msj .= '
							<div class="my-msg">
								<div class="my-content">
									T&uacute:
									<div class="my-thumbnail">
										'. $thumb .'
									</div>
									<div class="my-message">
										 ' . $mensaje->mensaje . '
									</div>
								</div>
							</div>
							<div class="timestamp_my_message"><p>' . $fecha_hora . '</p></div>
						';
					} else {


						$user_recibe = $brUsers->getUser($mensaje->id_envia)[0];

						if ( isset($user_recibe->user_thumbnail) &&  $user_recibe->user_thumbnail !== '') {

							$thumbr = '<img src="'. $user_recibe->user_thumbnail .'" class="img-chat-thumbnail" />';
						} else {
							$thumbr = '<span class="user-chat-recipe dashicons dashicons-admin-users"></span>';
						}

						$msj .= '
							<div class="your-msg">
								<div class="your-content">
									' . $user_recibe->first_name . ' ' . $user_recibe->last_name . ':
									<div class="your-thumbnail">
										'. $thumbr .'
									</div>
									<div class="your-message">
										'. $mensaje->mensaje .'
									</div>
								</div>
							</div>
							<div class="timestamp_your_message"><p>' . $fecha_hora . '</p></div>
						';

					}

				}

				$html = '
						<h2>Participantes </h2>
						<div id="chat">
							
							<div class="userChatList">
								' . $userList . '	
							</div>

							<div class="chat-panel">
								<div id="msg-history">
									' . $msj . '															
								</div>

								<form method="post" id="meetingForm" action="http://' . $_SERVER['HTTP_HOST'] . '/chat/">
									<input type="hidden" name="id_envia"  id="id_envia" value="' . wp_get_current_user()->ID . '" >
									<input type="hidden" name="id_recibe"  id="id_recibe" value="' . $firstUserId . '" >
									<label for="mensaje">Escriba un mensaje: </label>
									<textarea id="userMensaje" name="mensaje" type="text" rows="1" cols="50" value=""></textarea>
									<br>
									<input id="btnEnviaMsj" type="submit" >
								<form>
							</div>
						</div>';
					
				return $html;
			} else {
				$dashboard_data = '<h3>Debe iniciar sesi&oacuten para continuar</h3>
					<a href="' . wp_login_url( get_permalink() ) . '" title="Login">Iniciar Sesi&oacuten</a>
				';
				return $dashboard_data;
			}
		}


	}
 ?>