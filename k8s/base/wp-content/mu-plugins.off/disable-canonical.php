<?php
/*
Plugin Name: Disable Canonical Redirect (local)
*/
add_action('template_redirect', function () {
  remove_filter('template_redirect', 'redirect_canonical');
}, 0);
