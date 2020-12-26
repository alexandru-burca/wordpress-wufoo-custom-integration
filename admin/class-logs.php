<?php 
namespace WURE\ADMIN;

class LOGS{
    public $LOGS_LIST;

    function __construct(){
        add_filter( 'set-screen-option', array( __CLASS__, 'set_screen'), 10, 3 );
        add_action( 'wp_ajax_wure_view_logs', array($this, 'wure_view_logs') );
        add_action( 'wp_ajax_nopriv_wure_view_logs', array($this, 'wure_view_logs') );
    }

    public function register_sub_menu(){
        $hook = add_submenu_page( 'edit.php?post_type='.BASE::$post_type_slug, 'WURE Logs', 'Logs', BASE::$capability, 'wure-logs', array($this,'content') );
        add_action( "load-$hook", array( $this, 'screen_option') );
    }

    public function add_options(){
        $option = 'per_page';
        $args = array(
            'label' => 'Results',
            'default' => 10,
            'option' => 'results_per_page'
        );
        add_screen_option( $option, $args );
    }

    public function screen_option() {
        $option = 'per_page';
        $args   = [
            'label'   => 'Logs',
            'default' => 5,
            'option'  => 'logs_per_page'
        ];
        add_screen_option( $option, $args );
        $this->LOGS_LIST = new LOGS_LIST;
    }

    public function enqueue($hook_suffix){
        if($hook_suffix === 'wufoo-forms_page_wure-logs'):
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'wure-logs',  WURE_URL.'admin/resources/log-custom.js', array('jquery-ui-dialog'), filemtime( WURE_PATH.'/admin/resources/log-custom.js' ), true);
            wp_localize_script( 'wure-logs', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php'), 'security' => wp_create_nonce( 'wure-ajax-logs' ) ) );
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            wp_enqueue_style( 'wure-logs',  WURE_URL.'admin/resources/log-style.css', array('wp-jquery-ui-dialog'), filemtime( WURE_PATH.'/admin/resources/log-style.css' ));
        endif;
    }

    public function wure_view_logs(){
        check_ajax_referer('wure-ajax-logs', 'security');
        $id = $_POST['id'];
        $column = $_POST['column'];
        global $wpdb;
        $table = $wpdb->prefix.WURE_DB_TABLE;
        $result = $wpdb->get_results( $wpdb->prepare("SELECT * from {$table} WHERE id = %d", $id));
        if(count($result)){
            echo sprintf('<div class="wure-log-dialog-content"><textarea>%s</textarea></div>', $result[0]->$column);
        }else{
            echo 'Nothing Found';
        }

        wp_die();
    }


    public static function set_screen($status, $option, $value){
        return $value;
    }

    public function content(){
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Logs</h1>
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php
                            $this->LOGS_LIST->prepare_items();
                            $this->LOGS_LIST->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}


if(!class_exists('Link_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LOGS_LIST extends \WP_List_Table {

    public function __construct() {
        parent::__construct( [
            'singular' => 'Log',
            'plural'   => 'Logs',
            'ajax'     => false
        ] );
    }

    public static function get_logs( $per_page = 5, $page_number = 1 ) {

        global $wpdb;
        $table = $wpdb->prefix.WURE_DB_TABLE;
        $sql = "SELECT * FROM {$table}";

        if ( !empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= !empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        return $wpdb->get_results( $sql, 'ARRAY_A' );
    }

    public static function delete_log( $id ) {
        global $wpdb;
        $table = $wpdb->prefix.WURE_DB_TABLE;
        $wpdb->delete(
            "{$table}",
            [ 'id' => $id ],
            [ '%d' ]
        );
    }

    public static function record_count() {
        global $wpdb;
        $table = $wpdb->prefix.WURE_DB_TABLE;
        $sql = "SELECT COUNT(*) FROM {$table}";

        return $wpdb->get_var( $sql );
    }


    public function no_items() {
        echo 'No logs found.';
    }


    function column_name( $item ) {
        // create a nonce
        $delete_nonce = wp_create_nonce( 'wure_delete_log' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = array(
            'delete' => sprintf( '<a href="?page=%s&action=%s&logs=%s&_wpnonce=%s">Delete Logs</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }


    public function column_default( $item, $column_name ) {
        if($column_name == 'honeypot'){
            if($item[$column_name]){
                return '<span style="color:#dc3232">Blocked</span>';
            }else{
                return '<span style="color:#46b450">Allowed</span>';
            }
        }elseif ($column_name == 'nonce'){
            if($item[$column_name]){
                return '<span style="color:#46b450">Allowed</span>';
            }else{
                return '<span style="color:#dc3232">Blocked</span>';
            }
        }

        if(strlen($item[$column_name]) > 20){
            return sprintf('<a href="#" class="wure-view-in-popup" data-column="%s" data-id="%d">View</a>', $column_name, $item['id']);
        }
        return $item[$column_name];
    }


    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    function get_columns() {
        return array(
            'cb'      => '<input type="checkbox" />',
            'ip'    => 'IP',
            'browser' => 'Browser',
            'reCaptcha'    => 'reCaptcha',
            'wufoo' => 'Form',
            'data' => 'Data',
            'time' => 'Time',
            'referrer' => 'Referrer',
            'nonce' => 'Nonce',
            'honeypot' => 'Honeypot'
        );
    }

    /**
     * Columns to make sortable.
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'time' => array( 'time', true ),
            'nonce' => array( 'nonce', true ),
            'honeypot' => array( 'honeypot', true )
        );

        return $sortable_columns;
    }


    public function get_bulk_actions() {
        return array( 'bulk-delete' => 'Delete');
    }

    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'logs_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));
        $this->items = self::get_logs( $per_page, $current_page );
    }

    public function process_bulk_action() {
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_log( $id );
            }
        }
    }
}