<?php 

	/**
	 * 
	 */
	class BRProyecto
	{
		
		public static function getProyecto($id_participante = null)
		{

			global $wpdb;
			$br_proyecto = $wpdb->prefix . 'br_proyecto';

			if (isset($id_participante)) {
	
				$sql = "SELECT * FROM " . $br_proyecto . "
						WHERE id_participante = ". $id_participante;

				$result = $wpdb->get_results( $sql );
	
				return $result;
			}

		}

		public static function updateProyecto($id_participante = null, $datos = null)
		{

			global $wpdb;
			$br_proyecto = $wpdb->prefix . 'br_proyecto';
			$post = new BRCleanPostData;

			if ( isset($id_participante) && isset($datos) ) {

				$exist = $wpdb->get_results("SELECT * FROM ". $br_proyecto . " WHERE id_participante=" . $id_participante);
	
				if (count($exist) > 0) {

					$sql = "UPDATE ". $br_proyecto ." SET 
					        nombre = '" . (isset($datos['nombre-proyecto']) ? $post->cleanPost($datos['nombre-proyecto']) : '') . "',
							genero = '" . (isset($datos['genero-proyecto']) ? $post->cleanPost($datos['genero-proyecto']) : '') . "',
							formato = '" . (isset($datos['formato-proyecto']) ? $post->cleanPost($datos['formato-proyecto']) : '') . "',
							target = '" . (isset($datos['target-proyecto']) ? $post->cleanPost($datos['target-proyecto']) : '') . "',
							storyline = '" . (isset($datos['storyline-proyecto']) ? $post->cleanPost($datos['storyline-proyecto']) : '') . "',
							coproduccion = '" . (isset($datos['coproduccion-proyecto']) ? $post->cleanPost($datos['coproduccion-proyecto']) : '') . "'

							WHERE id_participante = ". $id_participante;

					$result = $wpdb->get_results( $sql );
		
					return $result;

				} else {
					
					$sql = "INSERT INTO " .$br_proyecto. "(id_participante,nombre,genero,formato,target,storyline,coproduccion) 
								VALUES(". $id_participante .",'" . (isset($datos['nombre-proyecto']) ? $post->cleanPost($datos['nombre-proyecto']) : '') . "','" . (isset($datos['genero-proyecto']) ? $post->cleanPost($datos['genero-proyecto']) : '') . "','" . (isset($datos['formato-proyecto']) ? $post->cleanPost($datos['formato-proyecto']) : '') . "','" . (isset($datos['target-proyecto']) ? $post->cleanPost($datos['target-proyecto']) : '') . "','" . (isset($datos['storyline-proyecto']) ? $post->cleanPost($datos['storyline-proyecto']) : '') . "','" . (isset($datos['coproduccion-proyecto']) ? $post->cleanPost($datos['coproduccion-proyecto']) : '') . "');";

					$result = $wpdb->get_results( $sql );
		
					return $result;
				}

			}

		}

	}

 ?>