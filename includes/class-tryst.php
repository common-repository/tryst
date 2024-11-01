<?php

/**
* The file that defines the core plugin class
*
* A class definition that includes attributes and functions used across both the
* public-facing side of the site and the admin area.
*
* @link       https://matteus.dev
* @since      1.0.0
*
* @package    Tryst
* @subpackage Tryst/includes
*/

/**
* The core plugin class.
*
* This is used to define internationalization, admin-specific hooks, and
* public-facing site hooks.
*
* Also maintains the unique identifier of this plugin as well as the current
* version of the plugin.
*
* @since      1.0.0
* @package    Tryst
* @subpackage Tryst/includes
* @author     Matteus Barbosa <contato@desenvolvedormatteus.com.br>
*/
class Tryst {
	
	/**
	* The loader that's responsible for maintaining and registering all hooks that power
	* the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      Tryst_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $loader;
	
	
	/**
	* The loader that's responsible for maintaining and registering all hooks that power
	* the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      Tryst_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $admin;

	protected $locale;
	
	public $path;
	
	
	/**
	* The public that's responsible for the client layer
	* the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      Tryst_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $public;
	
	/**
	* The unique identifier of this plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      string    $plugin_name    The string used to uniquely identify this plugin.
	*/
	protected $plugin_name;
	
	/**
	* The current version of the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      string    $version    The current version of the plugin.
	*/
	protected $version;
	
	/**
	* The current namespace of the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      string    $version    The current version of the plugin.
	*/
	protected $namespace;
	
	
	/**
	* The current domain of the plugin.
	*
	* @since    1.0.0
	* @access   protected
	* @var      string    $version    The current version of the plugin.
	*/
	protected $domain;
	
	
	/**
	* Define the core functionality of the plugin.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the admin area and
	* the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tryst';
		$this->path = plugin_dir_url( __FILE__ ).'../';
		
		$this->load_dependencies();
		$this->load_extensions();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->add_settings_menu();
		$this->createPages();
		
		if(class_exists("Tryst\\Domain\\Main")){
			$this->setNamespace("\Tryst\\Domain");
		}
		
	}
	
	/* 
	* Meeting view
	* Meeting list
	*/
	public function createPages(){
		
		if( null == get_page_by_title(__('Tryst Meeting', ' tryst'))){
			// Create post object
			$meeting_page = array(
				'post_title'    => __('Tryst Meeting', ' tryst'),
				'post_content'  => '<!-- wp:shortcode -->[tryst_meeting]<!-- /wp:shortcode -->',
				'post_type'		=> 'page',
				'post_status'   => 'publish',
				'post_author'   => 1
			);
			
			$meeting_page_id =  wp_insert_post( $meeting_page );
		}
			
		if(null == get_page_by_title(__('Tryst Meetings List', ' tryst'))){
			// Create post object
			$tryst_list_meetings = array(
				'post_title'    => __('Tryst Meetings List', ' tryst'),
				'post_content'  => '<!-- wp:shortcode -->[tryst_list_meetings]<!-- /wp:shortcode -->',
				'post_type'		=> 'page',
				'post_status'   => 'publish',
				'post_author'   => 1
			);
			
			$meetings_list_page_id =  wp_insert_post( $tryst_list_meetings );
		}
		
		
		
	}
	
	public function getInstance($object){
		return class_exists($this->getNamespace().'\\'.$object) ? new $this->getNamespace().'\\'.$object : null;
	}

	
	public function add_agenda_taxonomy(){
		register_taxonomy(
			'agenda',
			'tryst',
			array(
				'label' => __( 'Agenda' ),
				'rewrite' => array( 'slug' => 'agenda' ),
				'hierarchical' => false
			)
		);

		$exist = term_exists( __('Tryst Agenda', 'tryst'), 'agenda' );

		if(!$exist)
		wp_insert_term( __('Tryst Agenda', 'tryst'), 'agenda');

	}

	
		// Register Custom Post Type
	public function meeting_post_type() {
			$labels = array(
				'name'                  => _x( 'Meeting', 'Post Type General Name', 'tryst' ),
				'singular_name'         => _x( 'Meeting', 'Post Type Singular Name', 'tryst' ),
				'menu_name'             => __( 'Tryst', 'tryst' ),
				'name_admin_bar'        => __( 'Meeting', 'tryst' ),
				'archives'              => __( 'Item Archives', 'tryst' ),
				'attributes'            => __( 'Item Attributes', 'tryst' ),
				'parent_item_colon'     => __( 'Parent Item:', 'tryst' ),
				'all_items'             => __( 'All meetings', 'tryst'),
				'add_new_item'          => __( 'New Meeting', 'tryst' ),
				'add_new'               => __( 'New', 'tryst' ),
				'new_item'              => __( 'New', 'tryst' ),
				'edit_item'             => __( 'Edit meeting', 'tryst' ),
				'update_item'           => __( 'Update', 'tryst' ),
				'view_item'             => __( 'View', 'tryst' ),
				'view_items'            => __( 'View meeting', 'tryst' ),
				'search_items'          => __( 'Search meeting', 'tryst' ),
				'not_found'             => __( 'Not Found', 'tryst' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'tryst' ),
				'featured_image'        => __( 'Featured Image', 'tryst' ),
				'set_featured_image'    => __( 'Set featured image', 'tryst' ),
				'remove_featured_image' => __( 'Remove featured image', 'tryst' ),
				'use_featured_image'    => __( 'Use as featured image', 'tryst' ),
				'insert_into_item'      => __( 'Insert into item', 'tryst' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'tryst' ),
				'items_list'            => __( 'Items list', 'tryst' ),
				'items_list_navigation' => __( 'Items list navigation', 'tryst' ),
				'filter_items_list'     => __( 'Filter items list', 'tryst' )
			);
			$args = array(
				'label'                 => __( 'Meeting', 'tryst' ),
				'description'           => __( 'Register meetings to keep control of dates & times', 'tryst' ),
				'labels'                => $labels,
				'taxonomies'			=> [],
				'supports'              => ['title', 'custom-fields'],
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'page',
				'menu_icon' 			=> 'dashicons-calendar' 
			);

			register_post_type( 'tryst', $args );
		
		}
	
	/**
	* Adds a submenu under Settings menu
	*
	* @since    1.0.0
	* @access   private
	*/
	private function add_settings_menu() {
		require_once 'class-tryst-settings.php';
	}
	
	/**
	* Load the required dependencies for this plugin.
	*
	* Include the following files that make up the plugin:
	*
	* - Tryst_Loader. Orchestrates the hooks of the plugin.
	* - Tryst_i18n. Defines internationalization functionality.
	* - Tryst_Admin. Defines all hooks for the admin area.
	* - Tryst_Public. Defines all hooks for the public side of the site.
	*
	* Create an instance of the loader which will be used to register the hooks
	* with WordPress.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function load_dependencies() {
		
		/**
		* The class responsible for orchestrating the actions and filters of the
		* core plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tryst-loader.php';
		
		/**
		* The class responsible for defining internationalization functionality
		* of the plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tryst-i18n.php';
		
		/**
		* The class responsible for sending email
		* side of the site.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tryst-email.php';
		
		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tryst-admin.php';
		
		/**
		* The class responsible for defining all actions that occur in the public-facing
		* side of the site.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tryst-public.php';
		
		
		
	}
	
	
	/**
	* Load the optional extensions for this plugin.
	*
	* Include the following files that make up the plugin:
	*
	* - Tryst_Loader. Orchestrates the hooks of the plugin.
	* - Tryst_i18n. Defines internationalization functionality.
	* - Tryst_Admin. Defines all hooks for the admin area.
	* - Tryst_Public. Defines all hooks for the public side of the site.
	*
	* Create an instance of the loader which will be used to register the hooks
	* with WordPress.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function load_extensions() {
		
		$this->loader = new Tryst_Loader();
	}
	
	/**
	* Define the locale for this plugin for internationalization.
	*
	* Uses the Tryst_i18n class in order to set the domain and to register the hook
	* with WordPress.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function set_locale() {
		global $tryst_plugin;
		$options = get_option('tryst_option');
		$plugin_i18n = new Tryst_i18n($options['form_country']);
		$this->locale = $plugin_i18n;
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
		$this->loader->add_filter( 'plugin_locale', $plugin_i18n, 'force_locale_filter' );
	}

	public function getLocale(){
		return $this->locale;
	}
	
	/**
	* Register all of the hooks related to the admin area functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function define_admin_hooks() {
		
		$this->admin = new Tryst_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
		
		
	}
	
	/**
	* Register all of the hooks related to the public-facing functionality
	* of the plugin.
	*
	* @since    1.0.0
	* @access   private
	*/
	private function define_public_hooks() {
		
		$plugin_public = new Tryst_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		//$this->loader->add_action( 'after_setup_theme', $plugin_public, 'login_member', 0, 2 );
		$this->loader->add_action( 'wp_head', $plugin_public, 'hook_head' );
		$this->loader->add_action( 'init', $this, 'add_agenda_taxonomy' );
		if ( !post_type_exists( 'tryst' ) ) {
			$this->loader->add_action( 'init', $this, 'meeting_post_type' );
		}
		$plugin_public->shortcodes_load();
		
		$this->public = $plugin_public;
		
	}
	
	/**
	* Run the loader to execute all of the hooks with WordPress.
	*
	* @since    1.0.0
	*/
	public function run() {
		$this->loader->run();
		
		if(is_admin()){
			$this->admin->run();
		}		
	}
	
	/**
	* The name of the plugin used to uniquely identify it within the context of
	* WordPress and to define internationalization functionality.
	*
	* @since     1.0.0
	* @return    string    The name of the plugin.
	*/
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	/**
	* The reference to the class that orchestrates the hooks with the plugin.
	*
	* @since     1.0.0
	* @return    Tryst_Loader    Orchestrates the hooks of the plugin.
	*/
	public function get_loader() {
		return $this->loader;
	}
	
	/**
	* Retrieve the version number of the plugin.
	*
	* @since     1.0.0
	* @return    string    The version number of the plugin.
	*/
	public function get_version() {
		return $this->version;
	}
	
	
	/**
	* Get The current version of the plugin.
	*
	* @return  string
	*/ 
	public function getNamespace()
	{
		return $this->namespace;
	}
	
	/**
	* Set The current version of the plugin.
	*
	* @param  string  $namespace  $version The current version of the plugin.
	*
	* @return  self
	*/ 
	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
		
		return $this;
	}
	
	
	/* 
	* $ext string: member, payment, attendants, notification...
	* plugin must start with tryst-*
	*/
	public function isExtensionActive($ext){
		/**
		* Detect plugin. For use on Front End only.
		*/
		include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
		return is_plugin_active( 'tryst-'.$ext.'/tryst-'.$ext.'.php' ) ;
	}
	
	/* 
	* $ext string: member, payment, attendants, notification...
	* plugin must start with tryst-*
	*/
	public function getExtensionPath($ext){
		$dir = dirname(__FILE__) . '/../../tryst-'.$ext;

		if(!is_dir($dir)){
			throw new \Exception('Could not find extension');
		}

		return $dir;
	}
}