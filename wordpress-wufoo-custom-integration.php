<?php
/**
 * Plugin Name: Wufoo - Custom integration
 * Description: Integrate Wufoo with Wordpress using API
 * Version:     0.0.2
 * Plugin URI:  https://github.com/alexandru-burca/wordpress-wufoo-custom-integration
 * Author:      Alex Burca
 * Author URI:  https://www.linkedin.com/in/burca-alexandru/
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die();

//Define Global Constants
defined( 'WURE_PATH' ) or define( 'WURE_PATH', plugin_dir_path( __FILE__ ) );
defined( 'WURE_URL' )  or define( 'WURE_URL',  plugin_dir_url( __FILE__ ) );
defined( 'WURE_DB_TABLE' ) or define( 'WURE_DB_TABLE', 'wure_logs' );

class WURE_MAIN_CLASS{
     function __construct(){
         //WP Backend code
         require_once(WURE_PATH . "/admin/admin.php");

         //WP frontend code
         require_once(WURE_PATH . "/includes/wp/core.php");

         //Other Classes
         require_once(WURE_PATH . "/includes/class-logs.php");
         require_once(WURE_PATH . "/includes/class-form-errors.php");
         require_once(WURE_PATH . "/includes/class-nonce.php");
         require_once(WURE_PATH . "/includes/class-honeypot.php");
         require_once(WURE_PATH . "/includes/class-reCaptcha.php");
         require_once(WURE_PATH. "/includes/class-WufooHelper.php");

         //Build DB
         register_activation_hook( __FILE__, array($this, 'buildDB') );

         //Actions
         add_action( 'init', array ( $this, 'form'));
     }

     public function form(){
          if($_SERVER['REQUEST_METHOD'] == 'POST' 
          && isset($_POST['g-recaptcha-response']) 
          && isset($_POST['idForm'])):

               $formID = $_POST['idForm'];

               $log = new WURE\LOGS;
               $errors = new WURE\FormErrors($log, $formID);

               //Test WP Nounce
               $nonce = new WURE\NONCE($log, $errors);
               if( !$nonce->validate() ):
                    return;
               endif;

               //Test honeypot
               $honeypot = new WURE\HONEYPOT($log, $errors);
               if( !$honeypot->validate() ):
                    return;
               endif;

               //Test reCaptcha
               $reCaptcha = new WURE\RECAPTCHA($log, $errors);
               if( !$reCaptcha->validate() ):
                    return;
               endif;

               //Continue with Wufoo Helper
               $wufoo = new WURE\WufooHelper($log, $errors);
               $wufoo->process();
          endif;
     }

     public function buildDB(){
         require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

         global $wpdb;

         //Table Name
         $tableName = $wpdb->prefix.WURE_DB_TABLE;

         //Table structure
         $charset_collate = $wpdb->get_charset_collate();
         dbDelta( "CREATE TABLE $tableName (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              ip text DEFAULT NULL,
              referrer text DEFAULT NULL,
              browser text DEFAULT NULL,
              reCaptcha text DEFAULT NULL,
              wufoo text DEFAULT NULL,
              data text DEFAULT NULL,
              nonce tinyint(1) DEFAULT 1,
              honeypot tinyint(1) DEFAULT 0,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              PRIMARY KEY  (id)
         ) $charset_collate;" );
     }
}
$WURE = new WURE_MAIN_CLASS;