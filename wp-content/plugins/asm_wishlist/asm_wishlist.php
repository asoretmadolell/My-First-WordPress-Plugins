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