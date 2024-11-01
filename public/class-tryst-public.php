<?php
/**
* The public-facing functionality of the plugin.
*
* @link       https://matteus.dev
* @since      1.0.0
*
* @package    Tryst
* @subpackage Tryst/public
*/
/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    Tryst
* @subpackage Tryst/public
* @author     Matteus Barbosa <contato@desenvolvedormatteus.com.br>
*/
class Tryst_Public {
	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;
	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;
	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of the plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {
		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Tryst_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Tryst_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap-4.1.3.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-font-rubik', 'https://fonts.googleapis.com/css?family=Rubik', false );
		wp_enqueue_style( $this->plugin_name.'-vengine', plugin_dir_url( __FILE__ ) . 'jQuery-Validation-Engine-master/css/validationEngine.jquery.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tryst-public.css', array(), $this->version, 'all' );
	}
	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {
		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Tryst_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Tryst_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/
		if(!wp_script_is('jquery-3')){
			wp_enqueue_script('jquery-3',  plugin_dir_url( __FILE__ ) . 'js/jquery-3.2.1.slim.min');
		}
		wp_register_script('vengine-lang-js', plugin_dir_url( __FILE__ ) . 'jQuery-Validation-Engine-master/js/languages/jquery.validationEngine-pt_BR.js', ['jquery-3']);
		wp_enqueue_script('vengine-lang-js');
		wp_register_script('vengine-js', plugin_dir_url( __FILE__ ) . 'jQuery-Validation-Engine-master/js/jquery.validationEngine.js', ['jquery-3']);
		wp_enqueue_script('vengine-js');
		wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', ['jquery-3'], $this->version );
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap-4.1.3.bundle.min.js', ['jquery-3'], null, false);
		wp_enqueue_script( $this->plugin_name.'-js', plugin_dir_url( __FILE__ ) . 'js/tryst-public.js', array( 'bootstrap-js' ), null, false);
	}
	public function hook_head(){
		echo '<meta name="tryst_path" content="'.plugin_dir_url( __FILE__ ).'../'.'">';
	}
	public function login_member($login = null, $password = null){
		if($login == null)
		return;
		$creds = array(
			'user_login'    => $login,
			'user_password' => $password,
			'remember'      => true
		);
		$user = wp_signon( $creds, is_ssl() );
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);
		if ( is_wp_error( $user ) ) {
			echo $user->get_error_message();
		}
	}
	public static function process_query($query){
		if($query['tryst_meeting_hash'] != null)
		return Tryst_Public::shortcode_meeting(['tryst_meeting_hash' => $query['tryst_meeting_hash']]);
		if($query['agenda_id'] != null)
		return Tryst_Public::shortcode_agenda(['agenda_id' => $query['agenda_id'], 'form' => $query['form']]);
	}
	public static function shortcode_main($atts = null) {
		$query = shortcode_atts(array_merge([
			'form' => null,
			'tryst_meeting_hash' => null,
			'agenda_id' => null,
		], $_GET), $atts);
		return Tryst_Public::process_query($query);
	}
	private static function meeting_get($atts = null){
		global $tryst_plugin;
		if(isset($_GET['tryst_meeting_hash'])){
			$tryst_meeting_hash = sanitize_text_field($_GET['tryst_meeting_hash']);
		}
		if(isset($atts['tryst_meeting_hash'])){
			$tryst_meeting_hash = $atts['tryst_meeting_hash'];
		}
		if(!empty($tryst_meeting_hash)){
			$meeting = Tryst\Meeting::findByFormKey($tryst_meeting_hash);
			setup_postdata( $meeting->getPost() );
			$member = $meeting->getMember();
			$agenda = $meeting->getAgenda();
			$meeting_date =  $tryst_plugin->getLocale()->getDateFormatted($meeting->getTimestamp());
			include plugin_dir_path( __FILE__ ).'templates/form-meeting.php';
		}
	}
	/* 
	* when $_POST is sent
	*/
	private static function meeting_post($atts = null){
		global $tryst_plugin, $post, $wpdb;

		if(!isset($_POST) ){
			throw new \Exception('$_POST is missing');
		}


		if(!isset($_POST['meet'])){
			throw new \Exception('Meeting data is missing');
		}

	
		if($_POST['security_code'] != $_POST['security_code_repeat'])
		return __('Invalid post. Please insert the security code as requested.');
		$date = \DateTime::createFromFormat('d/m/Y', sanitize_text_field($_POST['meet']['date']));
		if(isset($_POST['agenda_id'])){
			//just reload the agenda to save with the meeting
			$agenda = new Tryst\Agenda(intval($_POST['agenda_id']), $date->format('m'), $date->format('Y'), []);
		} else {
			$agenda = new Tryst\Agenda(null, $date->format('m'), $date->format('Y'), []);
		}
		//persist
		//member from Member extension
		if(!empty($tryst_plugin) && $tryst_plugin->isExtensionActive('member')){
			if(!empty($tryst_plugin->getNamespace())){
				if(file_exists($tryst_plugin->getExtensionPath('member').'/includes/Tryst/Domain/Member.php')){
					$domain_class = "Tryst\\Domain\\Member";						
				}
			}

			if(isset($_POST['member'])){
				if(isset($domain_class) && class_exists($domain_class)){
					$member = new $domain_class(null, $_POST['member']);
				} else {					
					$member = new Tryst\Member(null, $_POST['member']);
				}
			}
		}
		if(isset($member)){
			$member->save();	
		} else {
			$member = null;
		}
		$meeting = new Tryst\Meeting(null, $_POST['meet'], null, $member);
		$meeting->save();
		self::meeting_mail($meeting, 'confirm');
		//do_action( 'after_setup_theme', $member->getLogin(), $member->getPassword() );
		echo '<script>location.href="'.get_page_link().'?tryst_meeting_hash='.$meeting->getFormKey().'"</script>';
	}
	private static function meeting_mail($meeting, $key){
		$mail = new Tryst_Email($meeting, $key);
		$options = unserialize(get_option('tryst_option'));
		if(!empty($meeting->getMember())){
			$mail->addRecipient($meeting->getMember()->getEmail());
		}
		$mail->addRecipient($options['tryst_mail']);
		return $mail->send();
	}
	public static function meeting_mail_repeat($hash){
		$meeting = Tryst\Meeting::findByFormKey($hash);
		self::meeting_mail($meeting, 'confirm-repeat');
		echo '<script>location.href="'.get_page_link().'?tryst_meeting_hash='.$meeting->getFormKey().'"</script>';
	}
	/* 
	* shortcode to process the meeting request form
	*/
	public static function shortcode_meeting($atts = null) {
		if(!isset($_POST['meeting_post']) && !isset($_GET['tryst_meeting_hash']) && !isset($_POST['meeting_mail_repeat']))
		return __('Please send meeting form or inform meeting ID');
		if(isset($_POST['meeting_mail_repeat']))
		return self::meeting_mail_repeat($_POST['tryst_meeting_hash']);
		if(isset($_POST['meeting_post']))			
		return self::meeting_post($atts);
		if(isset($_GET['tryst_meeting_hash']))
		return self::meeting_get($atts);					
	}
	private static function list_all_meetings(){
		echo '[TODO]';
	}
	private static function list_member_meetings($member_hash){
		global $tryst_plugin;
		$member = \Tryst\Member::findByFormKey($member_hash);		
		return $member->getMeetings();
	}
	public static function shortcode_list_meetings($atts = null) {
		global $tryst_plugin;
		wp_reset_postdata();
		$member_hash = isset($_GET['tryst_member_hash']) ? sanitize_text_field($_GET['tryst_member_hash']) : $atts['tryst_member_hash'];
		//$agenda_id = 202;
		ob_start();
		if(!empty($member_hash)){
			$meetings = self::list_member_meetings($member_hash);
		} else {
			$meetings = self::list_all_meetings();
		}
		require plugin_dir_path( __FILE__ ).'templates/meetings.php';
		return ob_get_clean();
	}
	public static function shortcode_agenda($atts = null) {
		global $tryst_plugin, $wpdb;
		wp_reset_postdata();
		if(isset($atts['form']))
		$form = $atts['form'];
		if(isset($_GET['form']))
		$form = sanitize_text_field($_GET['form']);
		if(isset($_GET['agenda_id'])){
			$agenda_id = intval($_GET['agenda_id']);
		}
		if(!isset($agenda_id)){
			$agenda_main_name = __('Tryst Agenda', 'tryst');
			$term = $wpdb->get_row( "SELECT term_id FROM {$wpdb->prefix}terms WHERE name = '$agenda_main_name' ", OBJECT );
			$agenda_id = $term->term_id;
		}
		//$agenda_id = 202;
		ob_start();
		$month = isset($_GET['tryst_mth']) ? intval($_GET['tryst_mth']) : date('m');
		$year = isset($_GET['tryst_yr']) ? intval($_GET['tryst_yr']) : date('Y');
		$agenda = new Tryst\Agenda($agenda_id, $month, $year);
		$agenda->save();
		require plugin_dir_path( __FILE__ ).'templates/calendar.php';

		require plugin_dir_path( __FILE__ ).'templates/modal-form-meeting.php'; 
		?> 
		<?php
		return ob_get_clean();
	}
	public function shortcodes_load(){
		add_shortcode( 'tryst', ['Tryst_Public', 'shortcode_main'] );
		add_shortcode( 'tryst_list_meetings', ['Tryst_Public', 'shortcode_list_meetings'] );
		add_shortcode( 'tryst_agenda', ['Tryst_Public', 'shortcode_agenda'] );
		add_shortcode( 'tryst_meeting', ['Tryst_Public', 'shortcode_meeting'] );
	}
	public static function tryst_register_query_vars( $vars ) {
		return $vars;
	}
}
add_filter( 'query_vars', ['Tryst_Public', 'tryst_register_query_vars'] );