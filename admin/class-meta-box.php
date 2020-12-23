<?php 
namespace WURE\ADMIN;

class CUSTOM_META_BOX {
    
    private $textarea = 'wufoo-form-html';
    private $wufooFormId = 'wufoo-form-id';
    private $metaBoxID = 'wufoo-form';
    private $shortcodeId = 'wure-shortcode';

    function __construct(){}

    public function add_meta_box(){
        add_meta_box( $this->metaBoxID, __( 'Wufoo Form', 'wure' ), array($this, 'meta_box_content'), BASE::$post_type_slug, 'normal', 'high' );
        add_meta_box( $this->shortcodeId.'-to-use', __( 'Shortcodes to use', 'wure' ), array($this, 'shortcodesToUse'), BASE::$post_type_slug, 'side' );
        add_meta_box( $this->shortcodeId, __( 'Form Shortcode', 'wure' ), array($this, 'shortcode'), BASE::$post_type_slug, 'side' );
    }

    public function meta_box_content($post){
        echo sprintf(
            '<h4>HTML</h4><textarea id="%s" name="%1$s">%2$s</textarea>',
            $this->textarea,
            htmlspecialchars(wp_unslash( get_post_meta( $post->ID, $this->textarea, true )))
        );

        echo sprintf(
            '<br><h4 style="margin-bottom:5px">Form id</h4><input id="%s" name="%1$s" value="%s" placeholder="Wufoo Form Id" style="width:100%%">',
            $this->wufooFormId,
            htmlspecialchars(wp_unslash( get_post_meta( $post->ID, $this->wufooFormId, true )))
        );
    }

    public function shortcode($post){
        echo sprintf("<input value='[wure-form id=\"%s\" title=\"%s\"]' id='shortcodeId' style='width:100%%' readonly><div id='shortcodeNotice' style='color:#46b450; padding:5px'></div>",
            $post->ID,
            $post->post_title
        );
    }

    public function shortcodesToUse(){
        echo '<ul><li>[wure-recaptcha]</li></ul>';
    }

    public function save_meta_box_content($postID){
        if( defined( 'DOING_AJAX' ) ) return;

        if( isset( $_POST[$this->textarea] ) ) update_post_meta( $postID, $this->textarea, $_POST[$this->textarea] );
        if( isset( $_POST[$this->wufooFormId] ) ) update_post_meta( $postID, $this->wufooFormId, $_POST[$this->wufooFormId] );
    }

    public function enqueue_code_editor(){
        if(BASE::isWurePostType()):
            wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
        endif;
    }

    public function initialize_code_editor(){
        if(BASE::isWurePostType()):
            ob_start();?>
            <script>
                //copy shortcode
                var copyShortcode = document.getElementById("shortcodeId");
                jQuery(copyShortcode).on('click', ()=>{
                    copyShortcode.select();
                    copyShortcode.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    jQuery('#shortcodeNotice').html('Shortcode Copied');
                    setTimeout(()=>{jQuery('#shortcodeNotice').html('')}, 3000);
                });
                
                //editor
                var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
                
                editorSettings.codemirror = _.extend(
                    {},
                    editorSettings.codemirror, 
                    {
                        indentUnit: 5,
                        tabSize: 2,
                        lineWrapping: true
                    }
                );
                wp.codeEditor.initialize( document.getElementById('<?php echo $this->textarea; ?>'), editorSettings );
            </script>
            <?php 
            echo ob_get_clean();
        endif;
    }
}