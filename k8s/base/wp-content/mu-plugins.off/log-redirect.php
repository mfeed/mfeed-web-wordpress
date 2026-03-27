<?php
add_filter('wp_redirect', function(, ){
  error_log('REDIRECT '..' -> '.);
  error_log(wp_debug_backtrace_summary(null, 0, false));
  return ;
}, 10, 2);
