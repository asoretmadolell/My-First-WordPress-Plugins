<?php

/*
 * Plugin Name: ASM Video Widget
 * Plugin URI: https://github.com/asoretmadolell/My-First-WordPress-Plugins
 * Description: A very dumb plugin that uses widgets and post metadata.
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

// show metabox in post editing page
// http://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
add_action('add_meta_boxes', 'asm_add_metabox');

function asm_add_metabox()
{
    // http://codex.wordpress.org/Function_Reference/add_meta_box
    // "$id" is for the HTML "id" attribute of the edit screen section
    // "$title" is for the title of the metabox in the edit screen section
    // "$callback" is the function name that will be executed when this is created
    // "$screen" is the type of element where this will be shown (e.g. a 'page' or a 'custom_post_type')
    add_meta_box('asm_youtube', 'YouTube Video Link', 'asm_youtube_handler', 'post');
}

/*
 * Metabox handler (the one specified in the "$callback" parameter of the function above)
 */
function asm_youtube_handler()
{
    // retrieve all metadata from the current post, using the global variable "$post"
    // http://codex.wordpress.org/Function_Reference/get_post_custom
    $value = get_post_custom( $post->ID ); // returns a multidimensional array of all the custom fields
    $youtube_link = $value['asm_youtube'][0]; // first and only element of the array "asm_youtube", created when saving
    $youtube_link = esc_attr( $youtube_link ); // we'll do this when saving anyway, but it's good to make it a habit
    echo '<label for="asm_youtube">YouTube Video Link</label> <input type="text" id="asm_youtube" name="asm_youtube" value="' . $youtube_link . '" />';
}

// save metabox data
// http://codex.wordpress.org/Plugin_API/Action_Reference/save_post
add_action('save_post', 'asm_save_metabox');

function asm_save_metabox( $post_id )
{
    // if it is an autosave action, exit the function
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    {
        return;
    }
    
    // if the user isn't allowed to edit the post, exit the function
    // http://codex.wordpress.org/Function_Reference/current_user_can
    // http://codex.wordpress.org/Roles_and_Capabilities#edit_posts
    if( !current_user_can( 'edit_posts' ) )
    {
        return;
    }
    
    $url = $_POST['asm_youtube']; // same "id" used in the HTML "input" element
    if( isset( $url ) ) // if the value of "asm_youtube" is being set
    {
        // http://codex.wordpress.org/Function_Reference/update_post_meta
        // "$post_id" is for the current post ID
        // "$meta_key" is the ID of our element (same one we used earlier when getting the post meta)
        // "$meta_value" the value that the user entered (since it is a URL, it is good habit to escape it)
        update_post_meta( $post_id, 'asm_youtube', esc_url( $url ) ); 
    }
}

// register widgets
// http://codex.wordpress.org/Widgets_API
add_action('widgets_init', 'asmvw_widget_init');

function asmvw_widget_init()
{
    register_widget( Asmvw_Widget );
}

/*
 * Creation of our new widget object
 */
class Asmvw_Widget extends WP_Widget
{
    // constructor, which will take care of title, description, CSS class, etc
    function Asmvw_Widget()
    {
        $widget_options = array(
            'classname' => 'asmvw_class', // CSS class
            'description' => 'Show a YouTube Video from a single post metadata' // description shown in widgets dashboard
        );
        
        // "$id_base" is the CSS ID
        // "$name" is the title shown in widgets dashboard
        // "$widget_options" is the array that we've just defined above
        $this->WP_Widget( 'asmvw_id', 'YouTube Video', $widget_options);
    }
    
    // widget options form when adding it to a sidebar in the widgets dashboard
    function form( $instance )
    {
        // first thing would be to assign some default values
        $defaults = array(
            'title' => 'Video'
        );
        
        // add the defaults into our instance
        // https://codex.wordpress.org/Function_Reference/wp_parse_args
        $instance = wp_parse_args( (array)$instance, $defaults );
        
        // grab the title that the user entered
        $title = esc_attr( $instance['title'] );
        
        // show the form
        echo '<p>Title <input type="text" class="widefat" name="' . $this->get_field_name( 'title' ) . '" value="' . $title . '" /></p>';
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
            
            // get post metadata
            $asmvw_youtube = esc_url( get_post_meta( get_the_ID(), 'asm_youtube', true ) );
            
            // print widget content
            echo '<iframe width="200" height="200" frameborder="0" allowfullscreen src="http://www.youtube.com/embed/' . get_youtube_id( $asmvw_youtube ) . '"></iframe>';
            
            echo $after_widget;
        }
    }
}

/*
 * Get YouTube video ID from link
 */
// http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
function get_youtube_id( $url )
{
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    return $my_array_of_vars['v'];
}