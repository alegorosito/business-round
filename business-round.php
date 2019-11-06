<?php 
/**
* Plugin Name: Business Round
* Description: This is a business round plugin.
* Version: 1.0.1
* Author: Alejandro Gabriel Gorosito
* Author URI: https://alegorosito.com
**/

	if ( ! defined( 'ABSPATH' ) ) {
	        exit;
	}

	/**
	 * 
	 */
	class BusinessRound 
	{
		
		function __construct()
		{

			/* 
			*	Create DB
			*/	
			require_once( plugin_dir_path( __FILE__ ) . '/conf/BRCleanPostData.php');
			require_once( plugin_dir_path( __FILE__ ) . '/conf/BRDatabase.php');
			require_once( plugin_dir_path( __FILE__ ) . '/conf/BRUser.php');
			require_once( plugin_dir_path( __FILE__ ) . '/conf/BRCalendar.php');
			require_once( plugin_dir_path( __FILE__ ) . '/conf/BRChat.php');
			require_once( plugin_dir_path( __FILE__ ) . '/conf/BRProyecto.php');

			/*
			* Import Views
			*/
			require_once( plugin_dir_path( __FILE__ ) . '/views/AdminUsersViews.php');
			require_once( plugin_dir_path( __FILE__ ) . '/views/AdminMeetingView.php');
			require_once( plugin_dir_path( __FILE__ ) . '/views/FrontUserDashboard.php');
			require_once( plugin_dir_path( __FILE__ ) . '/views/FrontUserProfile.php');

			register_activation_hook(__FILE__, array('BRDatabase', 'setup' ));

		}

		function register()
		{

			add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
			add_action('wp_enqueue_scripts', array( $this, 'enqueue' ) );
			
			/*
			* Backend
			*/
			add_action('admin_menu', array( $this , 'business_round_menu_option') );
			add_action('admin_action_update_user', array( 'BRUser' , 'userUpdate') );
			add_action('admin_action_create_new_user', array( 'BRUser' , 'userCreateNew') );
			add_action('wp_ajax_del_user', array( 'BRUser' , 'delUser') );
			add_action('wp_ajax_user_hs', array( 'BRCalendar' , 'getUserMeetings') );
			add_action('wp_ajax_del_meeting', array( 'BRCalendar' , 'delUserMeeting') );
			add_action('wp_ajax_user_msj', array( 'BRChat' , 'updateChatMessages') );



			/*
			* Frontend
			*/
			add_shortcode('br_user_dashboard', array( 'FrontUserDashboard', 'dashboard') );
			add_shortcode('br_user_players', array( 'FrontUserDashboard', 'playerList') );
			add_shortcode('br_user_calendar', array( 'FrontUserDashboard', 'calendar') );
			add_shortcode('br_user_meeting_form', array( 'FrontUserDashboard', 'meetingForm') );
			add_shortcode('br_user_wp_chat', array( 'FrontUserDashboard', 'wpChat') );
			add_shortcode('br_user_profile', array( 'FrontUserProfile', 'userProfile') );
			
						
		}

	

		function activate()
		 {
		 	$this->business_round_menu_option();
		 	$this->business_round_dashboard_function();
		 	flush_rewrite_rules();
		 } 

		function deactivate()
		{
			# code...
		}

		function uninstall()
		{
			# code...
		}

		function enqueue()
		{
			// Add main CSS
            wp_enqueue_style('mp-main-style', plugins_url('/assets/css/style.css', __FILE__) );
            // Add JS
            wp_enqueue_script('mp-front-jquery', 'https://code.jquery.com/jquery-3.4.1.min.js' );
            wp_enqueue_script('mp-front-script', plugins_url('/assets/js/scripts.js', __FILE__) );
            wp_enqueue_script('mp-main-validator', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.79/jquery.form-validator.js');
            wp_enqueue_script('mp-main-validator-lang', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.79/lang/es.js');
            wp_enqueue_script('mp-main-validator-size', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.79/file.js');

            /*
			* Dash icons
			*/
            wp_enqueue_style( 'dashicons' );

		}

		function enqueue_admin()
		{
			// Add main CSS
            wp_enqueue_style('mp-admin-form-style', plugins_url('/assets/css/admin.css', __FILE__) );
            // Add main JS
            wp_enqueue_script('mp-main-script', plugins_url('/assets/js/main.js', __FILE__) );
            wp_enqueue_script('mp-main-validator', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.79/jquery.form-validator.js');
            wp_enqueue_script('mp-main-validator-lang', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.79/lang/es.js');

		}


		// Plugin functions

		function business_round_menu_option()
		{


			/* Define Admin Pages*/
			$page =  
				array(
					'slug' => 'Mercado',
					'page_title' => 'Mercado',
					'menu_title' => 'Mercado',
					'capability' => 'manage_options',
					'menu_slug' => 'business-round-user-config',
					'callback' => array('AdminUsersViews','business_round_admin_users_view'),
					'icon_url' => 'dashicons-store',
					'position' => 2
				);
			
				add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['slug'], $page['callback'], $page['icon_url'], $page['position']);

			/* Define Admin SubPages*/
			$subpages = array(
				array(
					'parent_slug' => $page['slug'],
					'subpage_title' => 'Mercado',
					'menu_title' => 'Participantes',
					'capability' => 'manage_options',
					'menu_slug' => $page['slug'],
					'callback' => array('AdminUsersViews','business_round_admin_users_view'),
				),


				array(
					'parent_slug' => $page['slug'],
					'subpage_title' => 'Reuniones',
					'menu_title' => 'Reuniones',
					'capability' => 'manage_options',
					'menu_slug' => 'subpage-meeting-config',
					'callback' => array('AdminMeetingView','business_round_meeting_config'),
				),
			);

			foreach ($subpages as $key => $subpage) {
				add_submenu_page( $subpage['parent_slug'], $subpage['subpage_title'], $subpage['menu_title'], $subpage['capability'], $subpage['menu_slug'],$subpage['callback']);
			}



		}

		/*
		* BR Admin Config
		*/

		function registerCustomFields()
		{
			/* Register Settings */
			register_setting( $setting['option_group'], $setting['option_name'], ( isset($setting['callback'] ) ? $setting['callback'] : '' ) );

			/* Add Settings Section */
			add_settings_section( $id, $title, $callback, $page );

		}


	}

	if ( class_exists( 'BusinessRound' ) ) {
		$businessround = new BusinessRound();
		$businessround->register();
	}
	

 ?>
