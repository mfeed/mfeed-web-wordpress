<?php
$TREAT_IPV4_MAPPED_AS_IPV4 = true;


$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$by = 'REMOTE_ADDR';

if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {  
  $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
  $by = 'CF-Connecting-IP';
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
  $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
  $ip = trim($forwarded[0]); 
  $by = 'X-Forwarded-For:first';
}

function is_ipv4_mapped_ipv6(string $ip): bool {
  $bin = @inet_pton($ip);
  return $bin !== false && strlen($bin) === 16
      && substr($bin, 0, 12) === hex2bin('00000000000000000000ffff');
}

$is_v6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
if ($TREAT_IPV4_MAPPED_AS_IPV4 && $is_v6 && is_ipv4_mapped_ipv6($ip)) {
  $is_v6 = false; 
}
$label = $is_v6 ? 'IPv6' : 'IPv4';

// header('Cache-Control: no-store, max-age=0');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-T7PF10YCQ1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-T7PF10YCQ1');
  </script>
  <!-- End Global site tag (gtag.js) - Google Analytics -->
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php include("_include/header.php"); ?>
