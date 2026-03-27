<?php
/**
 * Log redirects (Location header) for debugging.
 */
add_filter('wp_redirect', function (, ) {
    error_log('REDIRECT '..' -> '.);
    // Stack summary (short)
    if (function_exists('wp_debug_backtrace_summary')) {
        error_log(wp_debug_backtrace_summary(null, 0, false));
    }
    return ;
}, 10, 2);
