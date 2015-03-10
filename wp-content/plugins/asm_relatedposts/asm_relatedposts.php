<?php

/*
 * Plugin Name: ASM Related posts
 * Plugin URI: https://github.com/asoretmadolell/My-First-WordPress-Plugins
 * Description: A very dumb plugin that loads posts from the database.
 * Version: 1.0
 * Author: Soret
 * Author URI: https://github.com/asoretmadolell
 * License: GPL2
 */

add_filter('the_content', 'asmrp_add_related_posts');

/*
 * Add links to related posts at the end of the document
 */
function asmrp_add_related_posts( $content )
{
    // if it's not a singular post, move on
    if( !is_singular('post') )
    {
        return $content;
    }
    
    // if it is, get the categories of that post
    // http://codex.wordpress.org/Function_Reference/get_the_terms
    $categories = get_the_terms( get_the_ID(), 'category');
    
    // we need the IDs of those categories. initializing the array
    $categoriesIds = array();
    
    // iterate through "$categories", grabbing its "term_id" and adding it to our "$categoriesIds"
    foreach ($categories as $category)
    {
        $categoriesIds[] = $category->term_id;
    }
    
    // creation of our loop query based on the category IDs we just grabbed
    // http://codex.wordpress.org/The_Loop
    // http://codex.wordpress.org/Class_Reference/WP_Query
    $loop = new WP_Query( array(
        'category__in' => $categoriesIds, // get posts in the categories specified
        'posts_per_page' => 4, // get only 4 results
        'post__not_in' => array( get_the_ID() ), // none of those results can be the post that is being viewed
        'orderby' => 'rand' // random order
    ) );
    
    // if there are posts in this loop
    if( $loop->have_posts() )
    {
        // start adding to the very end of "$content"
        $content .= 'RELATED POSTS:<br/><ul>';
        
        // iterate through the loop's posts until there are no more left
        while( $loop->have_posts() )
        {
            // get the current post
            $loop->the_post(); // this line of code overwrites the global variables that have the info of the post that is being viewed, saving this post's information instead
            // therefore, the next two methods will be getting the permalink and the title of this post, not the one that is being viewed
            // http://codex.wordpress.org/Template_Tags
            $content .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '<a></li>';
        }
        
        $content .= '</ul>';
    }
    
    // after the query is run, we need to reset data so that nothing remains in the global scope
    // http://codex.wordpress.org/Function_Reference/wp_reset_query
    wp_reset_query();
    
    return $content;
}