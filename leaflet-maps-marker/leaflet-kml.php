<?php
/*
 * kml feature removed due to request of WordPress plugin team (no direct access of plugin files allowed)
 * in order to use the this feature, please update to Maps Marker Pro
 *
*/
$kml_link = filter_var($_SERVER['SCRIPT_URI'], FILTER_SANITIZE_URL);
$cutoff_position = strpos($kml_link, 'wp-');
$redirect_url = substr($kml_link, 0, $cutoff_position);
header('Location: '.$redirect_url);
die();
?>