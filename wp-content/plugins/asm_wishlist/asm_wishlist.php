<?php

/*
 * Plugin Name: ASM Wishlist
 * Plugin URI: https://github.com/asoretmadolell/My-First-WordPress-Plugins
 * Description: A very dumb plugin that adds a wishlist widget where registered users can save the products they want to buy.
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

// executed when the menus are initiated
// http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
add_action('admin_menu', 'asmwp_plugin_menu');

function asmwp_plugin_menu()
{
    // https://codex.wordpress.org/Function_Reference/add_options_page
    // "$page_title" is for the title of the page
    // "$menu_title is the text used for the menu
    // "$capability" is the name of the capability required for the user to access this page
    // "$menu_slug" is a unique name used internally to refer to this menu
    // "$function" that is going to be called when you open this page
    add_options_page('ASM Wishlist Options', 'ASM Wishlist', 'manage_options', 'asmwp', 'asmwp_plugin_options');
}

// before adding anything to this page, we have to register these settings
// http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
add_action('admin_init', 'asmwp_admin_init');

/*
 * Here we will register two settings using the same group
 */
function asmwp_admin_init()
{
    // register the title shown to the user
    register_setting('asmwp_group', 'asmwp_dashboard_title');
    // register the number of elements that the user will be able to see
    register_setting('asmwp_group', 'asmwp_number_of_items');
}

/*
 * Options page callback, that shows our form with the settings previously registered
 */
function asmwp_plugin_options()
{
    ?>
    <div class="wrap">
        <?php screen_icon(); // deprecated? ?>
        <h2>ASM Wishlist</h2>
        <form action="options.php" method="post">
            <?php settings_fields('asmwp_group'); ?>
            <?php @do_settings_fields('asmwp_group') ?>
            <table class="form-table"> 
                <tr valign="top">
                    <th scope="row"><label for="asmwp_dashboard_title">Title</label></th>
                    <td>
                        <input type="text" name="asmwp_dashboard_title" id="dashboard_title" value="<?php echo get_option('asmwp_dashboard_title'); ?>" />
                        <br/><small>Dashboard widget title</small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="asmwp_number_of_items">Number of items</label></th>
                    <td>
                        <input type="text" name="asmwp_number_of_items" id="dashboard_title" value="<?php echo get_option('asmwp_number_of_items'); ?>" />
                        <br/><small>Number of items to show</small>
                    </td>
                </tr>
            </table>
            <?php @submit_button(); ?>
        </form>
    </div>
    <?php
}

// register widgets
// http://codex.wordpress.org/Widgets_API
add_action('widgets_init', 'asmwp_widget_init');

function asmwp_widget_init()
{
    register_widget( Asmwp_Widget );
}

/*
 * Creation of our new widget class
 */
class Asmwp_Widget extends WP_Widget
{
    // constructor, which will take care of title, description, CSS class, etc
    function Asmwp_Widget()
    {
        $widget_options = array(
            'classname' => 'asmwp_class', // CSS class
            'description' => 'Add items to wishlist' // description shown in widgets dashboard
        );
        
        // "$id_base" is the CSS ID
        // "$name" is the title shown in widgets dashboard
        // "$widget_options" is the array that we've just defined above
        $this->WP_Widget('asmwp_id', 'Wishlist');
    }
    
    // widget options form when adding it to a sidebar in the widgets dashboard
    function form( $instance )
    {
        // first thing would be to assign some default values
        $defaults = array(
            'title' => 'Wishlist'
        );
        
        // add the defaults into our instance
        // https://codex.wordpress.org/Function_Reference/wp_parse_args
        $instance = wp_parse_args( (array)$instance, $defaults );
        
        // grab the title that the user entered
        $title = esc_attr( $instance['title'] );
        
        // show the form
        echo '<p>Title <input class="widefat" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" /></p>';
    }
    
    // process information entered by the user and save it to the database
    function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        
        $instance['title'] = strip_tags( $new_instance['title'] );
        
        return $instance;
    }
    
    // show widget in post or page
    function widget( $args, $instance )
    {
        // extract "$args" so that the values are available as variables within this scope
        extract( $args );
        
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        // show widget only if it's a single post
        if( is_single() )
        {
            echo $before_widget;
            echo $before_title . $title . $after_title;
            
            // print widget content
            if( !is_user_logged_in() )
            {
                echo 'Please sign in to use this widget';
            }
            else
            {
                global $post;
                if( asmwp_has_wishlisted( $post->ID ) )
                {
                    echo 'You want this';
                }
                else
                {
                    echo '<span id="asmwp_add_wishlist_div"><a id="asmwp_add_wishlist" href="#">Add to wishlist</a></span>';
                }
            }
            
            echo $after_widget;
        }
    }
}

// executed immediately after the global WP class object is set up
// https://codex.wordpress.org/Plugin_API/Action_Reference/wp
add_action('wp', 'asmwp_init');

/*
 * Load external files
 */
function asmwp_init()
{
    // register script for later reference, not load it right away
    // https://codex.wordpress.org/Function_Reference/wp_register_script
    // "$handle" is for a unique name to use with "wp_enqueue_script()"
    // "$src" is for the URL to the script. never hardcore URLs to local scripts
    // "$deps" is for all the registered scripts that this particular script depends on, or the scripts that must be loaded before this one
    wp_register_script( 'asmwishlist-js', plugins_url( '/asm_wishlist.js', __FILE__ ), array( 'jquery' ) );
    
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'asmwishlist-js' );
    
    global $post;
    $data = array(
        'action' => 'asmwp_add_wishlist',
        'postId' => $post->ID
    );
    // make any data available to a script through a JavaScript variable
    // https://codex.wordpress.org/Function_Reference/wp_localize_script
    // "$handle" is for the registered script to attach the data to
    // "$name" is for the name of the variable that will contain the data. it has to be unique
    // "$data" is for the data itself as a signle or multi dimensional array
    wp_localize_script( 'asmwishlist-js', 'MyAjax', $data );
}

// create our own action by adding the prefix "wp_ajax_"
// https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_%28action%29
// http://solislab.com/blog/5-tips-for-using-ajax-in-wordpress/
add_action( 'wp_ajax_asmwp_add_wishlist', 'asmwp_add_wishlist_process' );
// if not logged in, use this prefix:  add_action( 'wp_ajax_nopriv_myajax-submit', 'myajax_submit' );

function asmwp_add_wishlist_process()
{
    // force it to be an int. it's important to validate your data
    $post_id = (int)$_POST['postId'];
    
    $user = wp_get_current_user();
    
    if( !asmwp_has_wishlisted( $post_id ) )
    {
        // add metadata to a user's record
        // https://codex.wordpress.org/Function_Reference/add_user_meta
        // "$user_id" is for the user to which the metadata corresponds
        // $meta_key is for the name of the metadata (recommended to add prefix)
        // $meta_value is for the content itself
        add_user_meta( $user->ID, 'asm_wanted_posts', $post_id );
    }
    
    echo 'You have added post ' . $post_id . ' to your wishlist';
    
    exit();
}

/*
 * Check if the user has wishlisted
 */
function asmwp_has_wishlisted( $post_id )
{
    $user = wp_get_current_user();
    
    // creates an array with every record that matches the metadata key and the user
    $values = get_user_meta( $user->ID, 'asm_wanted_posts' );
    
    foreach ( $values as $value )
    {
        if( $value == $post_id ) { return true; }
    }
}