<?php 
	/**
	 * 
	 */
	class FrontUserProfile
	{
		
		public static function userProfile()
		{


			require_once( dirname( __FILE__ ) . '/../../../../wp-admin/includes/image.php' );
			
			$wordpress_upload_dir = wp_upload_dir();
			
			$i = 1;  


			$html = '';

			if (is_user_logged_in()) {
				
				if (isset($_POST)) {

					if (isset($_POST['action'])) {
						if ($_POST['action'] == 'update_user_info') {
							$users = new BRUser;

							$users->userUpdateProfile(wp_get_current_user()->ID, $_POST);
						}

						if ($_POST['action'] == 'update_proyecto_info') {
							
							$proyecto = new BRProyecto;
							$proyecto->updateProyecto(wp_get_current_user()->ID, $_POST);
							
						}
					
						if ($_POST['action'] == 'update_user_name') {
							
							$users = new BRUser;
							$users->userNameUpdate($_POST);

						}

					}

				}
				

				$users = new BRUser;


				if (isset($_FILES)) {


					if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {

						$file = $_FILES['file'];
						$new_file_path = $file['tmp_name'];
						$new_file_mime = mime_content_type( $file['tmp_name'] );

						$userid = wp_get_current_user()->ID;
						$user = $users->getUser(wp_get_current_user()->ID)[0];

						if( empty( $file ) )
							die( 'File is not selected.' );
						 
						if( $file['error'] )
							die( $file['error'] );
						 
						if( $file['size'] > wp_max_upload_size() )
							die( 'It is too large than expected.' );
						 
						if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
							die( 'WordPress doesn\'t allow this type of uploads.' );
						 
						while( file_exists( $new_file_path ) ) {
							$i++;
							$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $user->first_name . '_' . $user->last_name . '_' . $user->id_wp_user . '_' . $file['name'];
						}

						if( move_uploaded_file( $file['tmp_name'], $new_file_path ) ) {
 
							$upload_id = wp_insert_attachment( array(
								'guid'           => $new_file_path, 
								'post_mime_type' => $new_file_mime,
								'post_title'     => preg_replace( '/\.[^.]+$/', '', $file['name'] ),
								'post_content'   => '',
								'post_status'    => 'inherit'
							), $new_file_path );
						 
							// wp_generate_attachment_metadata() won't work if you do not include this file
							require_once( ABSPATH . 'wp-admin/includes/image.php' );
						 
							// Generate and save the attachment metas into the database
							wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
						 
							$users->userUpdateThumbnail(wp_get_current_user()->ID, wp_get_attachment_url($upload_id));
						 
						}

					}

				}


				$userid = wp_get_current_user()->ID;
				$user = $users->getUser(wp_get_current_user()->ID)[0];



				$html = '

					<div class="user-form-tabs">
						<div class="user-form-tab" id="user-form-tab-profile">Perfil</div>
						<div class="user-form-tab" id="user-form-tab-personal-data">Datos Personales</div>
						
						<div class="user-form-tab-last" ></div>
					</div>

				';



				if (!isset($user->user_thumbnail) || ($user->user_thumbnail == '')) {
					$user_img = '<div class="user-no-thumbnail"><span class="dashicons dashicons-nametag"></span></div>';
				} else {
					$user_img = '<img class="user-thumbnail-form" src="' . $user->user_thumbnail . '">';
				}

				if ($user->tipo_participante == 'Productor') {
					$tipo_participante_val = 'Proyecto: ';
				} else {
					$tipo_participante_val = 'Empresa: ';
				}

				$html .= '
					<form method="post" enctype="multipart/form-data" class="form-style front-profile-form" id="profile-form-img-update">
				        
						<h2>Actualizar perfil</h2>

				        <div class="user-profile-img-form">
				        <small class="user-img-size">Recomendado 200x200px
				        	</small>
				        	<div>
					        	' . $user_img . '
				        	</div>
				        	<input type="file" name="file" accept="image/jpeg, image/jpg, image/png" data-validation="size" data-validation-max-size="2M" data-validation-dimension="min300x500" />

					        <small>Tama&ntildeo Max. de archivo: 2 MB</small>

				        </div>
				        <div class="user-profile-form-group">
				        	<input type="hidden" name="action" value="update_user_name"/>
					        <input type="hidden" name="userid" id="userid" value="' . $userid . '"/>
						    <label class="label-style" for="nombre">Nombre:</label>
							<input type="text" name="nombre" id="nombre" value="' . $user->first_name . '"/>
							<br>
							
							<label class="label-style" for="name">Apellido:</label>
							<input data-validation="length" data-validation-length="min5"  type="text" name="apellido" id="apellido" value="' . $user->last_name . '"/>

						    <br><br>

						    
				        </div>
			        	
					    <input type="submit" id="btn-image-upload"  name="submit_image_selector" value="Actualizar" />
			        	
				    </form>
				    <br>

					<form method="post" id="form-profile" class="form-style front-profile-form">
						<h2>Datos Personales</h2>
						<input type="hidden" name="action" value="update_user_info"/>
						<input type="hidden" name="userid" id="userid" value="' . $userid . '"/>
						
													
						<label class="label-style" for="empresa">' . $tipo_participante_val . '</label>
						<input data-validation="length" data-validation-length="min2"  type="text" name="empresa" id="empresa" value="' . $user->empresa . '" />
						
						<label class="label-style" for="telefono">Telefono: </label>
						<input data-validation="number"  data-validation-length="min5" type="number" name="telefono" id="telefono" value="' . $user->telefono . '" />
						
						<label class="label-style" for="email">E-mail: </label>
						<input data-validation="email"  data-validation-length="min5" type="text" name="email" id="email" value="' . $user->email . '" />
						
						<label class="label-style" for="ciudad">Ciudad: </label>
						<input data-validation="length" data-validation-length="min5"  type="text" name="ciudad" id="ciudad" value="' . $user->ciudad . '" />
						
						<label class="label-style" for="provincia">Provincia: </label>
						<input data-validation="length" data-validation-length="min5"  type="text" name="provincia" id="provincia" value="' . $user->provincia . '" />
						
						<label class="label-style" for="rol">Rol: </label>
						<input type="text" name="rol" id="rol" value="' . ucfirst($user->rol) . '" />
						
						<label class="label-style" for="dni">Documento: </label>
						<input data-validation="number" data-validation-length="min5" type="number" name="dni" id="dni" value="' . $user->dni . '" />
						
						<label class="label-style" for="repa">REPA: </label>
						<input types="number" type="text" name="repa" id="repa" value="' . $user->repa . '" />
						
						<input type="submit" name="submit" value="Actualizar" />
					</form>

				';


				return $html;	
			
			} else {

				$html = '<h3>Debe iniciar sesi&oacuten para continuar</h3>
						<a href="' . wp_login_url( get_permalink() ) . '" title="Login">Iniciar Sesi&oacuten</a>
					';
				return $html;

			}
		}
	}
 ?>