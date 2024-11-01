<?php

/**
 * Fired during plugin activation
 *
 * @link       https://matteus.dev
 * @since      1.0.0
 *
 * @package    Tryst
 * @subpackage Tryst/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tryst
 * @subpackage Tryst/includes
 * @author     Matteus Barbosa <contato@desenvolvedormatteus.com.br>
 */
class Tryst_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$options = [
			'tryst_meeting_request' => site_url('confirmacao-de-agendamento'),
			'form_country' => 'en-US',
			'email_footer' => '',
			'tryst_available_ops' => ["12:00", "14:00", "16:00"],
			'tryst_mail' => get_option('admin_email') 
		];
		
		update_option('tryst_option', serialize($options));
		$cat_main_name = __('Tryst Agenda', 'tryst');
		$cat_id = get_cat_ID($cat_main_name);
		if($cat_id == 0)
		$cat_id = wp_create_category($cat_main_name);
		
		$custom_fields = get_term_meta($cat_id);
		delete_term_meta($cat_id, 'tryst_available_ops');
		$available_ops = ["12:00", "14:00", "16:00"];
		add_term_meta($cat_id, 'tryst_available_ops', json_encode($available_ops));
		add_term_meta($cat_id, 'location', '');
	}
}
