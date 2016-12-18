<?php
/*
Plugin Name: Redirect 404
Plugin URI: https://github.com/tepez/yourls-redirect-404
Description: Redirect 404 errors to a custom error page
Version: 1.0
Author: Tom Yam
Author URI: https://github.com/tomyam1
*/

global $tp_yourls_redirect_404;

/*
* CONFIG: EDIT THIS
*/

/* The url where 404 errors are redirected to */
$tp_yourls_redirect_404['url'] = 'https://www.tepez.co.il/error-pages/short-url-not-found';

/* The QS parameter where the short url is given */
$tp_yourls_redirect_404['qs'] = 'shortUrl';

/*
* DO NOT EDIT FARTHER
*/

yourls_add_action( 'redirect_keyword_not_found', 'tp_redirect_404' );
yourls_add_action( 'loader_failed', 'tp_redirect_404' );

function tp_redirect_404( $data ) {
    global $tp_yourls_redirect_404;
    $shorturl = $data[0];

    $redirect_url = $tp_yourls_redirect_404['url'] .
        '?' .
        urlencode($tp_yourls_redirect_404['qs']) .
        '=' .
        urlencode($shorturl);

    yourls_redirect( $redirect_url, 302 );
}