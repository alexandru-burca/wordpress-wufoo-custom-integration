<?php
namespace WURE\ADMIN;

class BASE{
    public static $capability = 'manage_options';
    public static $post_type_slug = 'wufoo-forms';
    public static $svgIcon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100%" viewBox="99.087 125.492 86.439 72.032" enable-background="new 99.087 125.492 86.439 72.032" xml:space="preserve"><g id="Layer_2"><path d="M99.087,162.622c0.588,19.376,16.481,34.903,35.999,34.903c19.891,0,36.016-16.125,36.016-36.016 s-16.125-36.016-36.016-36.016c-19.519,0-35.411,15.527-35.999,34.903V162.622z"/><path d="M162.995,161.508c0,15.414-12.495,27.909-27.909,27.909c-15.414,0-27.909-12.495-27.909-27.909 s12.495-27.909,27.909-27.909C150.5,133.599,162.995,146.095,162.995,161.508z"/><path fill="#FFFFFF" d="M123.195,183.721l-17.273-34.254l11.766-3.731l9.148,20.483l1.256-21.99l10.224-1.973l9.435,20.125 l0.848-22.108l10.524-2.07l-3.013,39.137l-11.013,2.188l-9.506-19.694l-1.327,21.846L123.195,183.721L123.195,183.721z"/></g></svg>';

    public static function isWurePostType(){
        global $post;
        if ( ! $post ) { return; }
        if ( self::$post_type_slug === $post->post_type ):
            return true;
        else:
            return false;
        endif;
    }
}