<?php 
namespace WURE\ADMIN;

class CUSTOM_POST_TYPE{

    function __construct(){}

    public function register_post_type() {

        $labels = array(
            'name' => _x('Forms', 'plural'),
            'singular_name' => _x('Form', 'singular'),
            'menu_name' => _x('Wufoo Forms', 'admin menu'),
            'name_admin_bar' => _x('Wufoo Forms', 'admin bar'),
            'add_new' => _x('Add New', 'add new'),
            'add_new_item' => __('Add New Form'),
            'new_item' => __('New Form'),
            'edit_item' => __('Edit Form'),
            'view_item' => __('View Form'),
            'all_items' => __('All Forms'),
            'not_found' => __('No Forms found.'),
        );

        register_post_type(BASE::$post_type_slug,
            array(
                'supports' => array('title', 'revisions'),
                'labels' => $labels,
                'public' => false,
                'show_ui' => true,
                'exclude_from_search' => true,
                'query_var' => false,
                'has_archive' => false,
                'hierarchical' => false,
                'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode(BASE::$svgIcon),
                'capabilities' => array(
                    'edit_post'          => BASE::$capability,
                    'read_post'          => BASE::$capability,
                    'delete_post'        => BASE::$capability,
                    'edit_posts'         => BASE::$capability,
                    'edit_others_posts'  => BASE::$capability,
                    'delete_posts'       => BASE::$capability,
                    'publish_posts'      => BASE::$capability,
                    'read_private_posts' => BASE::$capability
                )
            )
        );
    }
}