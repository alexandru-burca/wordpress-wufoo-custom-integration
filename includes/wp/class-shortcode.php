<?php
namespace WURE\WP;

class SHORTCODE {
    public $content = '';
    public $formId = '';
    public $reCaptchaShortcode = '[wure-recaptcha]';
    function __construct(){}

    public function formShortcode($atts){
        $atts = shortcode_atts( array(
            'title' => '',
            'id' => ''
        ), $atts );

        $this->formId = $atts['id'];

        $this->getFormContent();
        $this->findAndRundreCaptchaShortcode();
        $this->updateFormHtml();
        return $this->content ;
    }

    protected function getFormContent(){
        $this->content = get_post_meta( $this->formId, 'wufoo-form-html', true );
    }

    protected function getWufooFormId(){
        return get_post_meta( $this->formId, 'wufoo-form-id', true );
    }

    protected function findAndRundreCaptchaShortcode(){
        $this->content = str_replace($this->reCaptchaShortcode, do_shortcode($this->reCaptchaShortcode), $this->content);
    }

    protected function updateFormHtml(){
        $position = strpos($this->content, '<form');
        //string positions start at 0, and not 1.
        if($position || $position === 0):
            $this->content =  substr_replace($this->content, ' data-wure="on" ', $position+6, 0);
        endif;

        //No need to verify if position is 0 since "</form>" can't be on position 0
        if($position = strpos($this->content, '</form>')):
            //Honeypot
            $extrafields = '<label class="first_name_wure"><input name="first-name" type="text" tabindex="-1" autocomplete="false"></label>';

            //The wufoo form id & the wp nonce
            $extrafields .= sprintf( 
                '<div style="display:none!important"><input type="hidden" id="idForm" name="idForm" value="%s">%s</div>',
                $this->getWufooFormId(),
                wp_nonce_field('wuresecretaction', 'wurenonce', true, false)
            );
            $this->content =  substr_replace($this->content, $extrafields , $position, 0);
        endif;
    }

    public function reCaptchaShortcode(){
        return sprintf('<div class="g-recaptcha" data-sitekey="%s"></div>',
            get_option('recaptcha_site_key')
        );
    }
}