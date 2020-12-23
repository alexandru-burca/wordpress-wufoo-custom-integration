<?php 
namespace WURE\ADMIN;

class LOGS{
    function __construct(){}

    public function register_sub_menu(){
        add_submenu_page( 'edit.php?post_type='.BASE::$post_type_slug, 'WURE Logs', 'Logs', BASE::$capability, 'wure-logs', array($this,'content') );
    }

    public function content(){
        echo sprintf('<div class="wrap"><h1>%s</h1>',
            esc_html( get_admin_page_title() )
        );

        if(file_exists(ABSPATH.'/wp-content/wure.log')):
            $log = file_get_contents(ABSPATH.'/wp-content/wure.log');
        else:
            $log = 'No records...';
        endif;

        echo sprintf('<textarea readonly style="width:100%%; margin-top:10px" rows="30">%s</textarea>',$log);
    }
}