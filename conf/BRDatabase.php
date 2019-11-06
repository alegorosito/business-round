<?php 

	/**
	 * 
	 */
	class BRDatabase
	{

		public static function setup()
		{
			global $wpdb;
			$br_participante = $wpdb->prefix . 'br_participante';
			$br_proyecto = $wpdb->prefix . 'br_proyecto';
			$br_solicitud_reunion = $wpdb->prefix . 'br_solicitud_reunion';
			$br_chat = $wpdb->prefix . 'br_chat';
			$last_chat = $wpdb->prefix . 'br_last_chat';

			$charsetcollect = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $br_participante (
						id int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
						id_wp_user int NOT NULL,
						tipo_participante varchar(20),
						empresa varchar(250),
						intereses varchar(250),
						formato varchar(250),
						telefono varchar(250),
						email varchar (250),
						ciudad varchar (250),
						provincia varchar (250),
						rol varchar (250),
						dni varchar (250),
						repa varchar (250),
						user_thumbnail varchar(250),
						proyecto varchar(250)
					);

					CREATE TABLE $br_proyecto (
						id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
						id_participante int NOT NULL,
						nombre varchar(50),
						genero varchar(50),
						formato varchar(50),
						target varchar(50),
						storyline varchar(250),
						coproduccion varchar(50)
					);

					CREATE TABLE $br_solicitud_reunion (
						id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
						id_solicita int NOT NULL,
						id_acepta int NOT NULL,
						fecha_hora timestamp NOT NULL,
						aceptado boolean NOT NULL
					);

					CREATE TABLE $br_chat (
						id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
						id_envia int NOT NULL,
						id_recibe int NOT NULL,
						fecha_hora timestamp NOT NULL,
						mensaje varchar(250) NOT NULL
					);

					CREATE TABLE $last_chat (
						id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
						current_user_chat int NOT NULL,
						last_chat_user_id int NOT NULL
					);

					";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php');


			 dbDelta( $sql );
		}

	}

 ?>