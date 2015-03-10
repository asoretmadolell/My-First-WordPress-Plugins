<?php

/*
 * Plugin Name: ASM Yahoo Finance
 * Plugin URI: https://github.com/asoretmadolell/My-First-WordPress-Plugins
 * Description: A very dumb plugin that asfgadsñoÑIUJHLIKJYUHLIHYK
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

add_action('init', 'asmyf_register_shortcodes');

/*
 * Registration of our shortcode
 */
// http://codex.wordpress.org/Shortcode_API
function asmyf_register_shortcodes()
{
    add_shortcode('rate', 'asmyf_rate');
}

/*
 * Function for the "rate" shortcode
 */
// "$args" is for the parameters entered in the shortcode
// "$content" is for what's between the shortcode with closing tag
function asmyf_rate( $args, $content )
{
    // get the arguments
    $from = $args['from'];
    $to = $args['to'];
    
    // build URL using the parameters
    // example of URL: http://finance.yahoo.com/d/quotes.csv?s=USDEUR=X&f=l1
    $url = 'http://finance.yahoo.com/d/quotes.csv?s=' . $from . $to . '=X&f=l1';
    
    // GET query using WordPress internal functions
    // http://codex.wordpress.org/Function_Reference/wp_remote_get
    // http://codex.wordpress.org/HTTP_API
    $result = wp_remote_get($url); // returns an array of results
    
    // build our resulting string using "esc_attr"
    // http://codex.wordpress.org/Function_Reference/esc_attr
    $result = '1 ' . $from . ' equals ' . $result['body'] . ' ' . $to;
    return esc_attr( $result . ' ' . $content );
}