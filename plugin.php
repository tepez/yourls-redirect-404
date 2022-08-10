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
$tp_yourls_redirect_404['url'] = $_ENV['YOURLS_REDIRECT_404_URL'];

/* The QS parameter where the short url is given */
$tp_yourls_redirect_404['qs'] = 'shortUrl';

/*
* DO NOT EDIT FARTHER
*/

/**
 * Based on http://stackoverflow.com/a/8891890/1705056
 * With added support for headers added by CloudFront
 **/
function tp_url_origin( $s, $use_forwarded_host = false ) {
    $ssl = $s['HTTP_X_FORWARDED_PROTO'] == 'https'
        || $s['HTTP_CLOUD_FRONT_FORWARDED_PROTO'] == 'https'
        || $s['HTTPS'] == 'on';
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );

    $port     = $s['HTTP_X_FORWARDED_PORT'] || $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;

    $host     = isset( $s['HTTP_X_FORWARDED_HOST'] )
        ? $s['HTTP_X_FORWARDED_HOST']
        : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function tp_full_url( $s, $use_forwarded_host = false ) {
    return tp_url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}

function tp_redirect_404() {
    global $tp_yourls_redirect_404;

    $absolute_url = tp_full_url( $_SERVER, true );

    $redirect_url = $tp_yourls_redirect_404['url'] .
        '?' .
        urlencode($tp_yourls_redirect_404['qs']) .
        '=' .
        urlencode($absolute_url);

    yourls_redirect( $redirect_url, 302 );
    exit;
}

if ($_ENV['ROOT_REDIRECT_URL']) {
    yourls_add_action( 'redirect_keyword_not_found', 'tp_redirect_404' );
    yourls_add_action( 'loader_failed', 'tp_redirect_404' );
}
