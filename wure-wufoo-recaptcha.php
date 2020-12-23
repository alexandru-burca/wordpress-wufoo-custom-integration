<?php
/**
 * Plugin Name: Wufoo - Custom integration
 * Description: Integrate Wufoo with Wordpress using API
 * Version:     0.0.1
 * Plugin URI:  https://github.com/alexandru-burca/wordpress-wufoo-custom-integration
 * Author:      Alex Burca
 * Author URI:  https://www.linkedin.com/in/burca-alexandru/
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die();

class WURE_MAIN_CLASS{
     function __construct(){}

     public function init(){
          add_action('init', array ( $this, 'form'));
     }

     public function require(){
          //WP Backend code
          require_once(__DIR__."/admin/admin.php");

          //WP frontend code 
          require_once(__DIR__."/includes/wp/core.php");

          //Other Classes
          require_once(__DIR__."/includes/class-logs.php");
          require_once(__DIR__."/includes/class-form-errors.php");
          require_once(__DIR__."/includes/class-nonce.php");
          require_once(__DIR__."/includes/class-honeypot.php");
          require_once(__DIR__."/includes/class-reCaptcha.php");
          require_once(__DIR__."/includes/class-WufooHelper.php");
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
}
$WURE = new WURE_MAIN_CLASS;
//Include all php classes
$WURE->require();
//Init the code
$WURE->init();