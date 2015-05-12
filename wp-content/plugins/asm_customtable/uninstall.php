<?php

// only execute the contents of this file if the plugin is really being uninstalled
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
{
    exit();
}

// 3. Plugin removal
    
// the same table name specified earlier
global $wpdb;
$tablename = $wpdb->prefix . "asm_hits";

// if the table exists, delete it
if( $wpdb->get_var( "SHOW TABLES LIKE '$tablename'" ) == $tablename )
{
    $sql = "DROP TABLE `$tablename`;";
    $wpdb->query( $sql );
}