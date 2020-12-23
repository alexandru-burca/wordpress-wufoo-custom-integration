<?php 
namespace WURE\ADMIN;

class SETTINGS_PAGE{

    function __construct(){}
    
    public function register_sub_menu(){
        add_submenu_page( 'edit.php?post_type='.BASE::$post_type_slug, 'WURE Settings', 'Settings', BASE::$capability, 'wure-settings', array($this,'content') );
    }

    public function content(){
        if ( ! current_user_can( BASE::$capability ) )  return;
    
        // add error/update messages
        if ( isset( $_GET['settings-updated'] ) ):
            add_settings_error( 'wure_messages', 'wure_message', 'Settings Saved', 'updated' );
        endif;
    
        // show error/update messages
        settings_errors( 'wure_messages' );
        ob_start();
        echo sprintf('<div class="wrap"><h1>%s</h1><form action="options.php" method="post">',
            esc_html( get_admin_page_title() )
        );
        settings_fields( 'wure-settings' );
        do_settings_sections( 'wure-settings' );
        submit_button( 'Save Settings' );

        echo '</form></div>';
    }

    public function register_settings(){
        $section_name = 'WURE';
        add_settings_section(
            $section_name,
            '<span id="epc_settings">Settings</span>',
            '__return_false',
            'wure-settings'
        );
        add_settings_field(
            'recaptcha-key',
            'Recaptcha Site KEY',
            array($this, 'input_field'),
            'wure-settings',
            $section_name,
            array(
                 'field' => 'recaptcha_site_key',
                 'type' => 'text'
            )
        );
       add_settings_field(
            'recaptcha-secret-key',
            'Recaptcha Secret KEY',
            array($this, 'input_field'),
            'wure-settings',
            $section_name,
            array(
                 'field' => 'recaptcha_secret_key',
                 'type' => 'text'
            )
       );
        add_settings_field(
            'wufoo-api-key',
            'Wufoo API Key',
            array($this, 'input_field'),
            'wure-settings',
            $section_name,
            array(
                'field' => 'wufoo_api_key',
                'type' => 'text'
            )
        );
        add_settings_field(
            'wufoo-subdomain-name',
            'Wufoo Subdomain',
            array($this, 'input_field'),
            'wure-settings',
            $section_name,
            array(
                'field' => 'wufoo_subdomain',
                'type' => 'text'
            )
        );
        register_setting( 'wure-settings', 'recaptcha_site_key' );
        register_setting( 'wure-settings', 'recaptcha_secret_key' );
        register_setting( 'wure-settings', 'wufoo_api_key' );
        register_setting( 'wure-settings', 'wufoo_subdomain' );    }

    public function input_field($args){
        echo sprintf(
            '<input name="%s" type="%s" value="%s" class="regular-text code">',
            $args['field'],
            $args['type'],
            get_option( $args['field'] )
        );
   }
}