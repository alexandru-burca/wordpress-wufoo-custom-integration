<?php
namespace WURE\ADMIN;

include_once(__DIR__.'/class-base.php');
include_once(__DIR__.'/class-post-type.php');
include_once(__DIR__.'/class-meta-box.php');
include_once(__DIR__.'/class-settings-page.php');
include_once(__DIR__.'/class-logs.php');

class ADMIN_HOOKS{
     function __construct(){
          $this->postType();
          $this->metaBox();
          $this->settingsPage();
          $this->logs();
     }
     
     protected function postType(){
          $post_type = new CUSTOM_POST_TYPE;
          add_action( 'init', array($post_type, 'register_post_type' ));
     }

     protected function metaBox(){
          $meta_box = new CUSTOM_META_BOX;
          add_action( 'add_meta_boxes', array($meta_box, 'add_meta_box'));
          add_action( 'admin_enqueue_scripts', array($meta_box, 'enqueue_code_editor'));
          add_action( 'admin_footer', array($meta_box, 'initialize_code_editor'));
          add_action( 'save_post', array($meta_box, 'save_meta_box_content' ));
     }

     protected function settingsPage(){
          $settingsPage = new SETTINGS_PAGE;
          add_action( 'admin_menu', array($settingsPage, 'register_sub_menu') );
          add_action( 'admin_init', array($settingsPage, 'register_settings') );
     }

     protected function logs(){
          $settingsPage = new LOGS;
          add_action( 'admin_menu', array($settingsPage, 'register_sub_menu') );
          add_action( 'admin_enqueue_scripts', array($settingsPage, 'enqueue'));
     }

     protected function general(){
         add_action( 'admin_enqueue_scripts', array($this, 'enqueue'));
     }
}

//Load Hooks
new ADMIN_HOOKS;