<?php 

/**
 * 
 */
class BRChat
{
	
	public static function sendMessage($id_envia = null, $id_recibe = null, $mensaje = null)
	{
		if (isset($id_envia) && isset($id_recibe) && isset($mensaje)) {
			
			global $wpdb;

			$br_chat = $wpdb->prefix . 'br_chat';

			$sql = "INSERT INTO $br_chat(id_envia, id_recibe, mensaje) 
					VALUES(" . $id_envia . ", " . $id_recibe . ", '" . $mensaje . "');";
			
			$result = $wpdb->get_results( $sql );

			return $result;

		}
	}

	public static function getMessages($id = null, $recibe = null)
	{
		if (isset($id)) {
			
			global $wpdb;

			$br_chat = $wpdb->prefix . 'br_chat';

			$sql = "SELECT * FROM " . $br_chat . " 
					WHERE (id_envia = " . $id . " AND id_recibe = " . $recibe .") OR
							(id_envia = " . $recibe . " AND id_recibe = " . $id .")
					ORDER BY id ASC;";
			
			$result = $wpdb->get_results( $sql );

			return $result;

		}
	}

	public static function getAvailableChats($id = null)
	{
		if (isset($id)) {
			
			global $wpdb;

			$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';

			$sql = "SELECT id_acepta, id_solicita, 
					(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'first_name' limit 1) as nombre_invita, 
					(select meta_value from wp_usermeta where user_id = id_solicita and meta_key = 'last_name' limit 1) as apellido_invita, 
					(select meta_value from wp_usermeta where user_id = id_acepta and meta_key = 'first_name' limit 1) as nombre_acepta, 
					(select meta_value from wp_usermeta where user_id = id_acepta and meta_key = 'last_name' limit 1) as apellido_acepta
						FROM " . $br_solicitud_reunion . "
						WHERE (id_solicita = " . $id . " 
								OR id_acepta = " . $id . ") 
								AND aceptado = true;";
			
			$result = $wpdb->get_results( $sql );

			return $result;

		}
	}

	public static function updateChatMessages()
	{
		$post = new BRCleanPostData;

		if (isset($_POST)) {
			
			if ( isset($_POST['id_envia']) && isset($_POST['id_recibe']) ) {
				
				global $wpdb;

				$br_chat = $wpdb->prefix . 'br_chat';

				$sql = "SELECT * FROM " . $br_chat . " 
						WHERE (id_envia = " . $post->cleanPost($_POST['id_envia']) . " AND id_recibe = " . $post->cleanPost($_POST['id_recibe']) .") OR
								(id_envia = " . $post->cleanPost($_POST['id_recibe']) . " AND id_recibe = " . $post->cleanPost($_POST['id_envia']) .")
						ORDER BY id ASC;";
				
				$result = $wpdb->get_results( $sql );

				echo json_encode($result);

				wp_die();

			}

		}
	}

	public static function getLastChatUserID($id = null)
	{
		if (isset($id)) {
			
				
				global $wpdb;

				$last_chat = $wpdb->prefix . 'br_last_chat';

				$sql = "SELECT last_chat_user_id FROM " . $last_chat . "
						WHERE current_user_chat = " . $id . " ORDER BY id DESC LIMIT 1";
				
				$result = $wpdb->get_results( $sql );

				if (isset($result[0]->last_chat_user_id)) {
					return $result[0]->last_chat_user_id;
				} else {
					return null;
				}


		}
	}

	public static function setLastChatUserID($id = null, $last_chat_user_id)
	{
		if (isset($id)) {
			
				global $wpdb;

				$last_chat = $wpdb->prefix . 'br_last_chat';

				$sql = "INSERT INTO " . $last_chat . "(current_user_chat, last_chat_user_id)
									VALUES (" . $id . ", " . $last_chat_user_id . ")
						";
				
				$result = $wpdb->get_results( $sql );

				return $result;
		}
	}

}

 ?>