<?php
class Tryst_Admin_Meta{
	private static $meeting;
	public static function getMeeting(){
		return self::$meeting;
	}
	/**
	* Adds a metabox to the right side of the screen under the â€œPublishâ€ box
	*/
	public static function add_meeting_metaboxes() {
		global $post;
		try{
			//loads the meeting on editor page
			self::$meeting = new \Tryst\Meeting($post->ID);
		} catch(\Exception $e){
			echo '<script>console.log('.$e->getMessage().')</script>';
		}
		//date
		add_meta_box(
			'meeting_form_date',
			__('Meeting Date', 'tryst'),
			['Tryst_Admin_Meta', 'meeting_form_date'],
			'tryst',
			'normal',
			'default'
		);
		//time
		add_meta_box(
			'meeting_form_time',
			__('Meeting Time', 'tryst'),
			['Tryst_Admin_Meta', 'meeting_form_time'],
			'tryst',
			'normal',
			'default'
		);
	}
	/**
	* Output the HTML for the metabox.
	*/
	public static function meeting_form_date() {
		global $post, $tryst_plugin;
		// Nonce field to validate form request came from current site
		wp_nonce_field( basename( __FILE__ ), 'meeting_fields' );
		$meta = get_post_meta( $post->ID, 'date', true );
		$date = new \DateTime($meta);
		// Output the field
		$date_formatted = $tryst_plugin->getLocale()->getDateFormatted($date);
		echo '<input type="text" name="meeting[date]" value="' . $date_formatted  . '" class="widefat">';
	}
	/**
	* Output the HTML for the metabox.
	*/
	public static function meeting_form_time() {
		global $post, $tryst_plugin;
		// Nonce field to validate form request came from current site
		wp_nonce_field( basename( __FILE__ ), 'meeting_fields' );
		$time = get_post_meta( $post->ID, 'time', true );
		echo '<input type="text" name="meeting[time]" value="' .  $time   . '" class="widefat">';
	}
}