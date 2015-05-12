<?php

/*
 * Plugin Name: ASM Custom Table
 * Plugin URI: https://github.com/asoretmadolell/My-First-WordPress-Plugins
 * Description: A very dumb plugin that creates and uses custom tables
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

// 1. Plugin activation
register_activation_hook( __FILE__, 'asmct_create_update_table' );

function asmct_create_update_table()
{
    // specify table name
    global $wpdb;
    $tablename = $wpdb->prefix . "asm_hits";
    
    // check if table exists, create it if it doesn't
    if( $wpdb->get_var( "SHOW TABLES LIKE '$tablename'" ) != $tablename )
    {
        // construct the SQL query to create the table
        $sql = "CREATE TABLE `$tablename` (
            `hit_id`         INT( 11 ) NOT NULL AUTO_INCREMENT ,
            `hit_ip`        VARCHAR( 100 ) NOT NULL ,
            `hit_post_id`   INT( 11 ) NOT NULL ,
            `hit_date`      DATETIME ,
            PRIMARY KEY (hit_id)
            );";
        
        // execute the query
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}

// 2. Plugin deactivation
register_deactivation_hook( __FILE__, 'asmct_deactivate');

function asmct_deactivate()
{
    error_log( 'plugin deactivated' );
}

// filters the content of the post after it is retrieved, but before it is printed
// https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content
add_action( 'the_content', 'asmct_save_hit' );

function asmct_save_hit( $content )
{
    // check if it's a single post. if not, return the content as is
    if( !is_single() )
    {
        return $content;
    }
    
    // grab the post information
    $post_id = get_the_ID();
    
    // grab the user's IP address
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // the same table name specified earlier
    global $wpdb;
    $tablename = $wpdb->prefix . "asm_hits";
    
    // array of the data to be inserted
    $newdata = array(
        'hit_ip' => $ip,
        'hit_date' => current_time( 'mysql' ),
        'hit_post_id' => $post_id
    );
    
    // insert the data
    $wpdb->insert( $tablename, $newdata );
    
    // remember, this is a filter, so you MUST return the content
    return $content;
}