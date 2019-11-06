<?php 

/*
 *  Funciones de Usuario
 */

class BRUser 
{
	public function getUser( $id = null )
	{
		global $wpdb;
		$br_participante = $wpdb->prefix . 'br_participante';
		$user_meta = $wpdb->prefix . 'usermeta';

		$sql = "SELECT id_wp_user, tipo_participante, empresa, telefono, email, ciudad, provincia, rol, dni, repa, intereses,  
				(select meta_value from wp_usermeta where user_id = id_wp_user and meta_key = 'first_name' limit 1) as first_name, 
				(select meta_value from wp_usermeta where user_id = id_wp_user and meta_key = 'last_name' limit 1) as last_name, user_thumbnail, proyecto  
				FROM " . $br_participante . " ";
		
		if ( isset($id) ) {

			$sql .= " WHERE " . $br_participante . ".id_wp_user = " .$id;

		} 

		$result = $wpdb->get_results( $sql );

		foreach ($result as $key => $user) {

			$u = get_user_by( 'id', $user->id_wp_user );
			$result[$key]->user_name =  $u->user_login;

		}

		return $result;
	}
	
	public function getPlayer( $id = null )
	{
		global $wpdb;
		$br_participante = $wpdb->prefix . 'br_participante';
		$user_meta = $wpdb->prefix . 'usermeta';

		$sql = "SELECT id_wp_user, tipo_participante, empresa, telefono, email, ciudad, provincia, rol, dni, repa, formato, 
				(select meta_value from wp_usermeta where user_id = id_wp_user and meta_key = 'first_name' limit 1) as first_name, 
				(select meta_value from wp_usermeta where user_id = id_wp_user and meta_key = 'last_name' limit 1) as last_name 
				FROM " . $br_participante . " ";
		
		if ( isset($id) ) {
			$sql .= " WHERE " . $br_participante . ".id_wp_user = " . $id . " AND tipo_participante = 'player'";
		} else {
			$sql .= " WHERE tipo_participante = 'player'";
		}

		$result = $wpdb->get_results( $sql );

		return $result;
	}

	public function getUsersAvailables( $id = null, $tipo_participante = null)
	{
		global $wpdb;

		$brUsers = new BRUser;
		$user = (($brUsers->getUser($id) !== null) ? $brUsers->getUser($id) : null);


		if (isset($user)) {
			
			$br_participante = $wpdb->prefix . 'br_participante';
			$user_meta = $wpdb->prefix . 'usermeta';

			$sql = "SELECT id_wp_user, tipo_participante, empresa, telefono, email, ciudad, provincia, rol, dni, repa, proyecto, user_thumbnail, (select meta_value from wp_usermeta where user_id = id_wp_user and meta_key = 'first_name' limit 1) as first_name, (select meta_value from wp_usermeta where user_id = id_wp_user and meta_key = 'last_name' limit 1) as last_name
					FROM " . $br_participante . " ";
			
			$sql .= " WHERE 
						id_wp_user NOT IN (
								SELECT  id_acepta
								FROM    " . $wpdb->prefix . "br_solicitud_reunion
								WHERE   id_solicita = " . $id . " )
						AND id_wp_user <> " . $id . " 
						AND id_wp_user NOT IN (
								SELECT  id_solicita
								FROM    " . $wpdb->prefix . "br_solicitud_reunion
								WHERE   id_acepta = " . $id . " )
						AND id_wp_user <> " . $id . "
						AND tipo_participante <> '" . $tipo_participante . "'
						";
			
			$result = $wpdb->get_results( $sql );

			return $result;
		}
		
	}
	
	public static function userNameUpdate($data = null)
	{
		if (isset($data)) {
			
			$post = new BRCleanPostData;	

			$dataU = array(
		    	'ID'	=> $post->cleanPost($data['userid']),
			    'first_name'   =>  $post->cleanPost($data['nombre']),
			    'last_name'   =>  $post->cleanPost($data['apellido']),
			);
			 
			$user_data = wp_update_user( $dataU );


		}
	}

	public static function userUpdate()
	{
		global $wpdb;
		
		$u = new BRUser;

		$namedata['userid'] = $_POST['id_wp_user']; 
		$namedata['nombre'] = $_POST['nombre'];
		$namedata['apellido'] = $_POST['apellido'];

		$u->userNameUpdate($namedata);

		$br_participante = $wpdb->dbname . '.' . $wpdb->prefix . 'br_participante';
		$post = new BRCleanPostData;

		$sql = $wpdb->prepare("UPDATE " . $br_participante . " 
				SET tipo_participante = '%s', 
					empresa = '%s', 
					telefono = '%s',
					email = '%s',
					ciudad = '%s',
					provincia = '%s',
					rol = '%s',
					dni = '%s',
					repa = '%s', 
					proyecto = '%s' 
				WHERE id_wp_user = %d", 

				$post->cleanPost($_POST['tipo_participante']), 
				$post->cleanPost($_POST['empresa']), 
				$post->cleanPost($_POST['telefono']), 
				$post->cleanPost($_POST['email']), 
				$post->cleanPost($_POST['ciudad']), 
				$post->cleanPost($_POST['provincia']), 
				$post->cleanPost($_POST['rol']), 
				$post->cleanPost($_POST['dni']), 
				$post->cleanPost($_POST['repa']), 
				$post->cleanPost($_POST['proyecto']),
				intval($post->cleanPost($_POST['id_wp_user'])));



		$result = $wpdb->query($sql);

		
		$url = "admin.php?page=Mercado";
		wp_redirect( $url );

		exit;

	}	

	public static function userCreateNew()
	{
		global $wpdb;
		$br_participante = $wpdb->prefix . 'br_participante';
		$post = new BRCleanPostData;

		// check if the username is taken
		$user_id = username_exists($post->cleanPost($_POST['usuario']));
		$user_email = $post->cleanPost($_POST['email']);

		// check that the email address does not belong to a registered user
		if (!$user_id && email_exists($user_email) === false) {
		
			$user_name = $post->cleanPost($_POST['usuario']);
			$user_password = $post->cleanPost($_POST['password']);
			$user_email = $post->cleanPost($_POST['email']);

			// create the user
		    $user_id = wp_create_user(
		        $user_name,
		        $user_password,
		        $user_email
		    );

		    $userdata = array(
		    	'ID'	=> $user_id,
		    	'user_login'	=> $user_name,
			    'first_name'   =>  $post->cleanPost($_POST['nombre']),
			    'last_name'   =>  $post->cleanPost($_POST['apellido']),
			    'user_email'   =>  $post->cleanPost($_POST['email']),
			);
 
			$user_id = wp_insert_user( $userdata ) ;

		    $sql = "INSERT INTO " . $br_participante . "(
		    			id_wp_user,
		    			tipo_participante, 
						empresa, 
						telefono,
						email,
						ciudad,
						provincia,
						rol,
						dni,
						repa,
						proyecto) 
					VALUES('" . $user_id . "','" . $post->cleanPost($_POST['tipo_participante']) . "', '" . $post->cleanPost($_POST['empresa']) . "', '" . $post->cleanPost($_POST['telefono']) . "', '" . $post->cleanPost($_POST['email']) . "', '" . $post->cleanPost($_POST['ciudad']) . "', '" . $post->cleanPost($_POST['provincia']) . "', '" . $post->cleanPost($_POST['rol']) . "', '" . $post->cleanPost($_POST['dni']) . "', '" . $post->cleanPost($_POST['repa']) . "', '" . $post->cleanPost($_POST['proyecto']) . "');
		    ";


			$result = $wpdb->query($sql);


			$redirect = add_query_arg( [ 'page' => 'Mercado' , 'new'=>'success'] , $redirect );
			wp_redirect( $redirect );
			exit;

		} else {

			$redirect = add_query_arg( [ 'page' => 'Mercado' , 'new'=>'failed'] , $redirect );
			wp_redirect( $redirect );
			exit;		

		}


	}
			
	public static function userUpdateProfile($id = null, $userProfile = null)
	{
		global $wpdb;
		$br_participante = $wpdb->prefix . 'br_participante';
		$post = new BRCleanPostData;

		if (isset($userProfile)) {

			$sql = $wpdb->prepare("UPDATE " . $br_participante . " 
					SET empresa = '%s', 
						telefono = '%s',
						email = '%s',
						ciudad = '%s',
						provincia = '%s',
						rol = '%s',
						dni = '%s',
						repa = '%s'
					WHERE id_wp_user = %d", 

					$post->cleanPost($userProfile['empresa']), $post->cleanPost($userProfile['telefono']), $post->cleanPost($userProfile['email']), $post->cleanPost($userProfile['ciudad']), $post->cleanPost($userProfile['provincia']), $post->cleanPost($userProfile['rol']), $post->cleanPost($userProfile['dni']), $post->cleanPost($userProfile['repa']), intval($id));

			$result = $wpdb->query($sql);

			return $result;
		}

	}
		
	public static function userUpdateThumbnail($id = null, $user_thumbnail = null)
	{
		global $wpdb;
		$br_participante = $wpdb->prefix . 'br_participante';

		$sql = "UPDATE " . $br_participante . " 
				SET user_thumbnail = '" . $user_thumbnail . "'
				WHERE id_wp_user = " . $id;
				
		$result = $wpdb->get_results( $sql );

		return $result;

	}

	public static function delUser($id = null)
		{
			global $wpdb;

			if (isset($_POST['id'])) {
				$br_participante = $wpdb->prefix . 'br_participante';
				
				$sql = 'DELETE FROM ' . $br_participante . '  
						WHERE id_wp_user = ' . intval($_POST['id']);

				$result = $wpdb->get_results( $sql );

				wp_delete_user( intval($_POST['id']) );

				echo json_encode(intval($_POST['id']));
			}

			wp_die();
		}

}

 ?>