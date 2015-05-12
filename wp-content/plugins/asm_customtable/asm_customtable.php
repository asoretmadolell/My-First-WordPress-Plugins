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
    error_log( 'plugin activated' );
}

// 2. Plugin deactivation
register_deactivation_hook( __FILE__, 'asmct_deactivate');

function asmct_deactivate()
{
    error_log( 'plugin deactivated' );
}

// 3. Plugin removal