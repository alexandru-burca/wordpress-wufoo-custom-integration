<?php
namespace WURE\WP;
include_once(__DIR__.'/class-shortcode.php');

class WP_FUNCTIONALITY {
    protected $scriptHandle = 'wure-recaptcha-library';
    function __construct(){
        $this->shortcodes();
        $this->frontEndScripts();
    }

    public function shortcodes(){
        $shortocodes = new SHORTCODE;
        add_shortcode('wure-form', array($shortocodes, 'formShortcode'));
        add_shortcode('wure-recaptcha', array($shortocodes, 'reCaptchaShortcode'));
    }

    public function frontEndScripts(){
        add_action( 'wp_head', array($this, 'reCaptchaJS') );
        add_action( 'wp_footer', array($this, 'submitFormJs') );
        add_action( 'wp_footer', array($this, 'css') );
    }

    public function reCaptchaJS(){
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    }

    public function submitFormJs(){
        ob_start(); ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('form').on('submit', function(e){
                    var $form = $(this);
                    if($form.data('wure')){
                        if ($('textarea[name=g-recaptcha-response]', $form).val() === "") {
                                event.preventDefault();
                                $('.g-recaptcha>div', $form).css({
                                'border-color':'red',
                                'border-style':'solid',
                                'border-width':'1px'
                                });
                        }
                    }
                });
            });
        </script>
        <?php echo ob_get_clean();
    }

    public function css(){
        ob_start(); ?>
            <style>form label.first_name_wure{display:none!important}</style>
        <?php echo ob_get_clean();
    }
}

new WP_FUNCTIONALITY;