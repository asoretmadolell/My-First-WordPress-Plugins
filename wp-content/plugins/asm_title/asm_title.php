<?php

/*
 * Plugin Name: ASM Title
 * Plugin URI: https://github.com/asoretmadolell
 * Description: A very dumb plugin that modifies titles.
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

// http://codex.wordpress.org/Plugin_API/Filter_Reference
add_filter('the_title', 'asmtitle_title');
add_filter('the_content', 'asmtitle_content');
add_filter('list_cats', 'asmtitle_categories');

/*
 * Modify the title
 */
function asmtitle_title( $text )
{
    return 'OMG! ' . $text;
}

/*
 * Modify the content
 */
function asmtitle_content( $text )
{
    return strtoupper( $text );
}

/*
 * Modify categories
 */
function asmtitle_categories( $text )
{
    return strtolower( $text );
}