<?php

/*
 * Plugin Name: ASM Cron Email
 * Plugin URI: https://github.com/asoretmadolell/My-First-WordPress-Plugins
 * Description: A very dumb plugin that creates a cimple cron job.
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

add_action( 'init', 'asmcron_init_cronjob' );

/*
 * Initiating the cron job
 */
function asmcron_init_cronjob()
{
    // check that it hasn't been scheduled yet using our own custom hook
    if( !wp_next_scheduled( 'asmcron_sendmail_hook' ) )
    {
        // if it hasn't, schedule it
        // https://codex.wordpress.org/Function_Reference/wp_schedule_event
        // "$timestamp" is for the first time you want the event to occur
        // "$recurrence" is for how often the event should reoccur
        // "$hook" is for the action hook to execute
        wp_schedule_event( time(), 'hourly', 'asmcron_sendmail_hook' );
    }
}

add_action( 'asmcron_sendmail_hook', 'asmcron_sendmail' );

/*
 * Send Email
 */
function asmcron_sendmail()
{
    // get information about the site
    // https://codex.wordpress.org/Function_Reference/get_bloginfo
    $asm_admin_email = get_bloginfo( 'admin_email' );
    
    // send the e-mail
    // https://codex.wordpress.org/Function_Reference/wp_mail
    // "$to" is for the recipient
    // "$subject" is for the subject of the message
    // "$message" is for the message content itself
    wp_mail( $asm_admin_email, 'admin', 'Time for your medication!' );
}

