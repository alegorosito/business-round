<?php 
	/**
	 * 
	 */
	class BRCalendar
	{
		public static function userMeetings($user = null)
		{
			if (isset($user)) {
				
				global $wpdb;
				$br_participante = $wpdb->prefix . 'br_participante';
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

				$sql = "SELECT id_solicita, id_acepta, fecha_hora, aceptado FROM " . $br_solicitud_reunion . "
						WHERE id_solicita =	" . intval($user);

				$result = $wpdb->get_results( $sql );

				return $result;
			} elseif (!isset($user)) {
				
				global $wpdb;
				$br_participante = $wpdb->prefix . 'br_participante';
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

				$sql = "SELECT id, 
								id_solicita, 
								id_acepta,
								(select meta_value from wp_usermeta where user_id = id_acepta and meta_key = 'first_name' limit 1) as nombre_acepta,
								(select meta_value from wp_usermeta where user_id = id_acepta and meta_key = 'last_name' limit 1) as apellido_acepta, 
								(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'first_name' limit 1) as nombre_solicita,
								(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'last_name' limit 1) as apellido_solicita,
								fecha_hora, aceptado FROM " . $br_solicitud_reunion;

				$result = $wpdb->get_results( $sql );

				return $result;
			}
		}

		public static function userAcceptedMeetings($id = null)
		{
			if (isset($id)) {
				
				global $wpdb;
				$br_participante = $wpdb->prefix . 'br_participante';
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

				$sql = "SELECT id_solicita, 
								id_acepta, 
								(select meta_value from wp_usermeta where user_id = id_acepta and meta_key = 'first_name' limit 1) as nombre_acepta,
								(select meta_value from wp_usermeta where user_id = id_acepta and meta_key = 'last_name' limit 1) as apellido_acepta, 
								(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'first_name' limit 1) as nombre_invita,
								(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'last_name' limit 1) as apellido_invita, 
								fecha_hora, 
								aceptado 
						FROM " . $br_solicitud_reunion . "
						WHERE " . $br_solicitud_reunion .".id_solicita =" . intval($id) . 
							" OR " . $br_solicitud_reunion .".id_acepta =" . intval($id) .
							" AND aceptado = true;";

				$result = $wpdb->get_results( $sql );

				return $result;
			}
		}


		public static function userPendingMeetings($user = null)
		{
			if (isset($user)) {
				
				global $wpdb;
				$br_participante = $wpdb->prefix . 'br_participante';
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

				$sql = "SELECT id, id_solicita, 
								id_acepta, 
								(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'first_name' limit 1) as nombre_solicita,
								(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'last_name' limit 1) as apellido_solicita, 
								fecha_hora, 
								aceptado 
						FROM " . $br_solicitud_reunion . "
						WHERE " . $br_solicitud_reunion .".id_acepta =" . intval($user) .
							" AND aceptado = false;";

				$result = $wpdb->get_results( $sql );

				return $result;
			}
		}

		public static function getInterval($time = null, $id_solicita = null)
		{
			if (isset($time)) {
				
				global $wpdb;
				$br_participante = $wpdb->prefix . 'br_participante';
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

				$sql = "SELECT id_solicita, id_acepta, fecha_hora, aceptado FROM " . $br_solicitud_reunion . "
						WHERE fecha_hora =	'2019-11-07 " . $time . "' AND ( id_solicita = " . $id_solicita . 
						' OR id_acepta ='. $id_solicita .')';

				$result = $wpdb->get_results( $sql );

				return $result;
			}
		}
	
		public static function createMeeting($time = null, $id_solicita = null, $id_acepta = null)
		{
				global $wpdb;
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

				/* Verifica que no exista una reunion previa */
				$sql = "SELECT id FROM " . $br_solicitud_reunion . " WHERE id_solicita = " . $id_solicita . " 
							AND id_acepta = " .$id_acepta;
				$existe = $wpdb->get_results( $sql );

				if (isset($time) and isset($id_solicita) and isset($id_acepta) and (count($existe) < 1)) {
	
					$time = explode(" - ", $time);

					$sql = 'INSERT INTO ' . $br_solicitud_reunion . '(id_solicita,id_acepta,fecha_hora,aceptado) 
							VALUES('. $id_solicita .','. $id_acepta .',"2019-11-07 ' . $time[0] . ':00", 0)

					';

					$result = $wpdb->get_results( $sql );

					return $result;

				}

		}

		public static function getUserMeetings()
		{
			global $wpdb;



			if (isset($_POST)) {
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';
				$id = intval($_POST['id']);
				
				$sql = 'SELECT fecha_hora FROM ' . $br_solicitud_reunion . '
						WHERE aceptado = true and (id_acepta = ' . $id . ' OR id_solicita = ' . $id . ');';

				$result = $wpdb->get_results( $sql );

				echo json_encode( $result );
			}
			
			wp_die();

		}

		public static function delUserMeeting()
		{
			global $wpdb;

			if (isset($_POST)) {
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';
				$id = ($_POST['id']);
				$id = explode('-', $id);
				$id = intval($id[1]);
				
				$sql = "DELETE FROM " . $br_solicitud_reunion .
						" WHERE id = " . $id;

				$result = $wpdb->get_results( $sql );

				echo json_encode($id);
			}
			
			wp_die();

		}
		
		public static function updateMeeting($id = null, $fecha_hora = null)
		{
			global $wpdb;

			if (isset($id) and isset($fecha_hora)) {
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';
				
				$sql = 'UPDATE ' . $br_solicitud_reunion . ' SET aceptado = true, fecha_hora = "' . $fecha_hora . '" 
						WHERE id = ' . $id;

				$result = $wpdb->get_results( $sql );

				return $result;
			}
		}
		
		public static function removeMeeting($id = null)
		{
			global $wpdb;

			if (isset($id)) {
				$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';
				
				$sql = 'DELETE FROM ' . $br_solicitud_reunion . '  
						WHERE id = ' . $id;

				$result = $wpdb->get_results( $sql );

				return $result;
			}
		}

	}

 ?>