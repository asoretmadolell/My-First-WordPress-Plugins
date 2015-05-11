jQuery( document ).ready( function($)
{
    $( '#asmwp_add_wishlist' ).click( function(e)
    {
        // https://api.jquery.com/jquery.post/
        
        // "url" is for the URL to which the request is sent
        var host = document.location.host;
        if( host === 'localhost' ) { var dev = 'wordpress-test'; } else { var dev = ''; };
        var url = document.location.protocol + '//' + host + '/' + dev + '/wp-admin/admin-ajax.php';
        
        // "data" is for the parameters to send
        /* we decided to fill this array using PHP, in order to access the post ID
        var data = {
            action: 'asmwp_add_wishlist', // the action we created without the prefix (mandatory)
            postId: 100 // this will be the ID of the post in the future
        };
        */
        
        // "success" is a callback executed if the request succeeds
        function success( response )
        {
            alert ( response );
        }
        
        // finally, the ".post" method
        $.post( url, MyAjax, success );
    });
    
    // AJAX error handler, called only when an AJAX request completes with an error
    // https://api.jquery.com/ajaxError/
    $( document ).ajaxError( function( event, request, settings, thrownError )
    {
        alert( "The following errors occurred: " + thrownError );
    });
});