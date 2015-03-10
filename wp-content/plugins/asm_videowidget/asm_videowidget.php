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
    $value = get_post_custom( $post->ID ); // returns a multidimensional array
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