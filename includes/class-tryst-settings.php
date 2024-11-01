<?php

namespace Tryst;

class TrystSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            __('Tryst', 'tryst'), 
            __('Tryst', 'tryst'),
            'manage_options', 
            'tryst-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'tryst_option' );
        ?>
        <div class="wrap">
            <h1><?php _e('Basic information setup', 'tryst'); ?></h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'tryst_group' );
                do_settings_sections( 'tryst-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {     

        register_setting(
            'tryst_group', // Option group
            'tryst_option', // Option name
            ['sanitize_callback' => array( $this, 'sanitize' ) ]
        );

        add_settings_section(
            'tryst_default_section', // ID
            __('Tryst', 'tryst'), // Title
            array( $this, 'print_section_info' ), // Callback
            'tryst-setting-admin' // Page
        );  

        add_settings_field(
            'tryst_meeting_request', // ID
            __('Tryst meeting request route', 'tryst'), // Title 
            array( $this, 'tryst_meeting_request_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_default_section' // Section           
        );   



        add_settings_field(
            'tryst_available_ops', // ID
            __('Available agenda', 'tryst'), // Title 
            array( $this, 'tryst_available_ops_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_default_section' // Section           
        );   

        
        add_settings_field(
            'date_lock', // ID
            __('Date Lock', 'tryst'), // Title 
            array( $this, 'date_lock_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_default_section'
        ); // Section   



        add_settings_field(
            'location', // ID
            __('Location', 'tryst'), // Title 
            array( $this, 'location_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_default_section' // Section           
        );   

        add_settings_field(
            'form_country', // ID
            __('Country', 'tryst'), // Title 
            array( $this, 'form_country_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_default_section' // Section           
        );           


        add_settings_section(
            'tryst_email_section', // ID
            __('Tryst E-mail Settings', 'tryst'), // Title
            array( $this, 'email_section_info' ), // Callback
            'tryst-setting-admin' // Page
        );  

        add_settings_field(
            'tryst_mail', // ID
            __('E-mail account', 'tryst'), // Title 
            array( $this, 'tryst_mail_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_email_section' // Section           
        );   

        add_settings_field(
            'Email Footer', // ID
            __('Email Footer', 'tryst'), // Title 
            array( $this, 'email_footer_callback' ), // Callback
            'tryst-setting-admin', // Page
            'tryst_email_section' // Section           
        );   



    
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        global $tryst_plugin;
        $new_input = array();
        
        if( isset( $input['tryst_mail'] ) )
        $new_input['tryst_mail'] = sanitize_email( $input['tryst_mail'] );

        if( isset( $input['email_footer'] ) )
        $new_input['email_footer'] = sanitize_text_field( $input['email_footer'] );

        if( isset( $input['date_lock'] ) )
        $new_input['date_lock'] = sanitize_text_field( $input['date_lock'] );

        if( isset( $input['tryst_meeting_request'] ) )
        $new_input['tryst_meeting_request'] = sanitize_text_field( $input['tryst_meeting_request'] );

        if( isset( $input['tryst_available_ops'] ) )
        $new_input['tryst_available_ops'] = sanitize_text_field( $input['tryst_available_ops'] );

        if( isset( $input['location'] ) )
        $new_input['location'] = sanitize_text_field( $input['location'] );

        if( isset( $input['form_country'] ) )
        $new_input['form_country'] = sanitize_text_field( $input['form_country'] );


        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
      
    }

    
    /** 
     * Print the Section text
     */
    public function email_section_info()
    {
      
    }



    
        /* Get the settings option array and print one of its values
    */
   public function tryst_mail_callback()
   {
       printf(
           '<input type="email" id="tryst_mail" name="tryst_option[tryst_mail]" value="%s" />',
           isset( $this->options['tryst_mail'] ) ? esc_attr( $this->options['tryst_mail']) : ''
       );
   }

          /* Get the settings option array and print one of its values
    */
    public function email_footer_callback()
    {
        printf(
            '<textarea name="tryst_option[email_footer]" />%s</textarea>',
            isset( $this->options['email_footer'] ) ? esc_attr( $this->options['email_footer']) : ''
        );
    }


 /* Get the settings option array and print one of its values
    */
    public function date_lock_callback()
    {
        printf(
            '<input type="text" id="date_lock" name="tryst_option[date_lock]" value="%s" />',
            isset( $this->options['date_lock'] ) ? esc_attr( $this->options['date_lock']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function tryst_meeting_request_callback()
    {
        printf(
            '<input type="text" id="tryst_meeting_request" name="tryst_option[tryst_meeting_request]" value="%s" />',
            isset( $this->options['tryst_meeting_request'] ) ? esc_attr( $this->options['tryst_meeting_request']) : ''
        );
    }

    
 

    /** 
     * Get the settings option array and print one of its values
     */
    public function tryst_available_ops_callback()
    {
        printf(
            '<input type="text" id="tryst_available_ops" name="tryst_option[tryst_available_ops]" value="%s" />',
            isset( $this->options['tryst_available_ops'] ) ? esc_attr( $this->options['tryst_available_ops']) : ''
        );
    }

        /** 
     * Get the settings option array and print one of its values
     */
    public function location_callback()
    {
        printf(
            '<input type="text" id="location" name="tryst_option[location]" value="%s" />',
            isset( $this->options['location'] ) ? esc_attr( $this->options['location']) : ''
        );
    }

               /* Get the settings option array and print one of its values
    */
    public function form_country_callback()
    {
        $select = '<select id="form_country" name="tryst_option[form_country]" value="%s">';
        $countries = defined('TRYST_LIST_COUNTRY_SUPPORT') ? TRYST_LIST_COUNTRY_SUPPORT : ['en-US'];
        foreach($countries as $c){
            $select .= sprintf('<option %s>%s</option>', isset( $this->options['form_country'] ) && $this->options['form_country'] === $c ? 'selected' : '', $c);
        }
        echo $select;        
    }
}

if( is_admin() )
    $tryst_settings_page = new TrystSettingsPage();