<?php
/*
 * qr feature removed due to request of WordPress plugin team (no direct access of plugin files allowed)
 *
*/
$qr_link = filter_var($_SERVER['SCRIPT_URI'], FILTER_SANITIZE_URL);
$cutoff_position = strpos($qr_link, 'wp-');
$redirect_url = substr($qr_link, 0, $cutoff_position);
header('Location: '.$redirect_url);
die();
?>