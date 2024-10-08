<?php
/*
    Tools - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-tools.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php'); 
require_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php' );
WP_Filesystem();
global $wpdb, $wp_filesystem, $allowedtags;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$markercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_markers.'');
$layercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.'') - 1;
$action = isset($_POST['action']) ? sanitize_key($_POST['action']) : (isset($_GET['action']) ? sanitize_key($_GET['action']) : '');

if (!empty($action)) {
	$toolnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($toolnonce, 'tool-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according admin page!','leaflet-maps-marker').''); };
	if ($action == 'mass_assign') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `layer` = %d WHERE `layer` = %d", $_POST['layer_assign_to'], $_POST['layer_assign_from'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('All markers from layer ID %1$s have been successfully assigned to layer ID %2$s','leaflet-maps-marker'), htmlspecialchars($_POST['layer_assign_from']), htmlspecialchars($_POST['layer_assign_to'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';

	}
	elseif ($action == 'basemap') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `basemap` = %s", $_POST['basemap'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('The basemap for the selected markers has been successfully set to %1$s','leaflet-maps-marker'), htmlspecialchars($_POST['basemap'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'overlays') {
		$overlays_checkbox = isset($_POST['overlays_custom']) ? '1' : '0';
		$overlays2_checkbox = isset($_POST['overlays_custom2']) ? '1' : '0';
		$overlays3_checkbox = isset($_POST['overlays_custom3']) ? '1' : '0';
		$overlays4_checkbox = isset($_POST['overlays_custom4']) ? '1' : '0';
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s", $overlays_checkbox, $overlays2_checkbox, $overlays3_checkbox, $overlays4_checkbox );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The overlays status for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'wms') {
		$wms_checkbox = isset($_POST['wms']) ? '1' : '0';
		$wms2_checkbox = isset($_POST['wms2']) ? '1' : '0';
		$wms3_checkbox = isset($_POST['wms3']) ? '1' : '0';
		$wms4_checkbox = isset($_POST['wms4']) ? '1' : '0';
		$wms5_checkbox = isset($_POST['wms5']) ? '1' : '0';
		$wms6_checkbox = isset($_POST['wms6']) ? '1' : '0';
		$wms7_checkbox = isset($_POST['wms7']) ? '1' : '0';
		$wms8_checkbox = isset($_POST['wms8']) ? '1' : '0';
		$wms9_checkbox = isset($_POST['wms9']) ? '1' : '0';
		$wms10_checkbox = isset($_POST['wms10']) ? '1' : '0';
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d", $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox );
		$wpdb->query( $result );
		echo '<p><div class="updated" style="padding:10px;">' . __('The WMS status for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'mapsize') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d", $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('The map size for the selected markers has been successfully set to width =  %1$s %2$s and height = %3$s px','leaflet-maps-marker'), intval($_POST['mapwidth']), htmlspecialchars($_POST['mapwidthunit']), htmlspecialchars($_POST['mapheight'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'zoom') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `zoom` = %d", $_POST['zoom'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('Zoom level for the selected markers has been successfully set to %1$s','leaflet-maps-marker'), intval($_POST['zoom'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'controlbox') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `controlbox` = %d", $_POST['controlbox'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Controlbox status for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'panel') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `panel` = %d", $_POST['panel'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Panel status for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'icon') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `icon` = %s", $_POST['icon'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The icon for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'openpopup') {
		$popuptext = preg_replace("/\t/", " ", $_POST['popuptext']); //info: tabs break geojson
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `popuptext` = %s", $popuptext );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The popup status for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'popuptext') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `popuptext` = %s", $_POST['popuptext'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The popup text for the selected markers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'basemap-layer') {
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `basemap` = %s", $_POST['basemap-layer'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('The basemap for all layers has been successfully set to %1$s','leaflet-maps-marker'), htmlspecialchars($_POST['basemap-layer'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'overlays-layer') {
		$overlays_checkbox = isset($_POST['overlays_custom-layer']) ? '1' : '0';
		$overlays2_checkbox = isset($_POST['overlays_custom2-layer']) ? '1' : '0';
		$overlays3_checkbox = isset($_POST['overlays_custom3-layer']) ? '1' : '0';
		$overlays4_checkbox = isset($_POST['overlays_custom4-layer']) ? '1' : '0';
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s", $overlays_checkbox, $overlays2_checkbox, $overlays3_checkbox, $overlays4_checkbox );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The overlays status for all layers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'wms-layer') {
		$wms_checkbox = isset($_POST['wms-layer']) ? '1' : '0';
		$wms2_checkbox = isset($_POST['wms2-layer']) ? '1' : '0';
		$wms3_checkbox = isset($_POST['wms3-layer']) ? '1' : '0';
		$wms4_checkbox = isset($_POST['wms4-layer']) ? '1' : '0';
		$wms5_checkbox = isset($_POST['wms5-layer']) ? '1' : '0';
		$wms6_checkbox = isset($_POST['wms6-layer']) ? '1' : '0';
		$wms7_checkbox = isset($_POST['wms7-layer']) ? '1' : '0';
		$wms8_checkbox = isset($_POST['wms8-layer']) ? '1' : '0';
		$wms9_checkbox = isset($_POST['wms9-layer']) ? '1' : '0';
		$wms10_checkbox = isset($_POST['wms10-layer']) ? '1' : '0';
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d", $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox );
		$wpdb->query( $result );
		echo '<p><div class="updated" style="padding:10px;">' . __('The WMS status for all layers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'mapsize-layer') {
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d", $_POST['mapwidth-layer'], $_POST['mapwidthunit-layer'], $_POST['mapheight-layer'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('The map size for all layers has been successfully set to width =  %1$s %2$s and height = %3$s px','leaflet-maps-marker'), htmlspecialchars($_POST['mapwidth-layer']), htmlspecialchars($_POST['mapwidthunit-layer']), htmlspecialchars($_POST['mapheight-layer'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'zoom-layer') {
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `layerzoom` = %d", $_POST['zoom-layer'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('Zoom level for all layers has been successfully set to %1$s','leaflet-maps-marker'), htmlspecialchars($_POST['zoom-layer'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'controlbox-layer') {
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `controlbox` = %d", $_POST['controlbox-layer'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Controlbox status for all layers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'panel-layer') {
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `panel` = %d", $_POST['panel-layer'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Panel status for all layers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'listmarkers-layer') {
		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `listmarkers` = %d", $_POST['listmarkers-layer'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The list marker-status for all layers has been successfully updated','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'mass_delete_from_layer') {
		$result = $wpdb->prepare( "DELETE FROM `$table_name_markers` WHERE `layer` = %d", $_POST['delete_from_layer']);
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('All markers from layer ID %1$s have been successfully deleted','leaflet-maps-marker'), htmlspecialchars($_POST['delete_from_layer'])) . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
	}
	elseif ($action == 'mass_delete_all_markers') {
		$result = "DELETE FROM `$table_name_markers`";
		$wpdb->query( $result );
  		$delete_confirm_checkbox = isset($_POST['delete_confirm_checkbox']) ? '1' : '0';
	  	if ($delete_confirm_checkbox == 1) {
			echo '<p><div class="updated" style="padding:10px;">' . __('All markers from all layers have been successfully deleted','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Please confirm that you want to delete all markers by checking the checkbox','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
		}
	} 
	elseif ($action == 'database_downgrade') {
		//info: remove JSON encoding
		$markers = $wpdb->get_results('SELECT id,layer FROM '.$wpdb->prefix.'leafletmapsmarker_markers');
		foreach($markers as $marker){
			if (is_numeric($marker->layer) === FALSE) { //info: just convert non-numeric values
				$layer = json_decode($marker->layer, TRUE);
				$wpdb->update( $wpdb->prefix . 'leafletmapsmarker_markers', 
					array('layer'=> $layer[0]), //info: just take first element, discard others
					array('id'=>$marker->id)
					);
				unset($layer);
			}
		}
		delete_option('leafletmapsmarkerpro_license_key_trial');
		echo '<p><div class="updated" style="padding:10px;">' . __('Database downgrade is finished.','leaflet-maps-marker') . '</div><br/><a class="button-secondary" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'leaflet-maps-marker') . '</a></p>';
		echo '<script type="text/javascript">
			jQuery(function($) {
				$(document).ready(function(){
					$("#database-downgrade").hide();
				});
			});
		</script>';
	}
} else {
	$layerlist = $wpdb->get_results('SELECT * FROM `' . $table_name_layers . '` WHERE `id` > 0', ARRAY_A);
	?>
	<h1><?php _e('Tools','leaflet-maps-marker'); ?></h1>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	
	<?php
	echo __('Please use the following tools with care and consider making a database backup or export first, as most actions cannot be undone:','leaflet-maps-marker');
	echo '
	<ul>
	<li>- <a href="#backup-restore" style="text-decoration:none;">' . __('Backup/Restore settings','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#deleted-maps-errors" style="text-decoration:none;">' . __('Active shortcodes with invalid map IDs','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#move-markers" style="text-decoration:none;">' . __('Move markers to a layer','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#bulk-update-markers" style="text-decoration:none;">' . __('Bulk updates for marker maps','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#bulk-update-layers" style="text-decoration:none;">' . sprintf( esc_attr__('Bulk updates for all %1$s existing layer maps','leaflet-maps-marker'), $layercount_all) . '</a></li>
	<li>- <a href="#change-marker-id" style="text-decoration:none;">' . __('Change marker ID','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#change-layer-id" style="text-decoration:none;">' . __('Change layer ID','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#delete-selected-markers" style="text-decoration:none;">' . __('Delete all markers from a layer','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#delete-all-markers" style="text-decoration:none;">' . sprintf( esc_attr__('Delete all %1$s markers from all %2$s layers','leaflet-maps-marker'), $markercount_all, $layercount_all) . '</a></li>
	<li>- <a href="#marker-validity-check" style="text-decoration:none;">' . __('Marker validity check for layer assignements','leaflet-maps-marker') . '</a></li>
	<li>- <a href="#initialize-map-texts-wpml" style="text-decoration:none;">' .  __('Initialize map texts for translations','leaflet-maps-marker') . '</a></li>
	</ul>';
	?>
	<a name="backup-restore"></a>
	<br/>
	<form method="post">
	<input type="hidden" name="action" value="update-settings" />
	<?php wp_nonce_field('tool-nonce');
	$serialized_options = serialize($lmm_options);
	?>
	<table class="widefat" style="width:100%;height:100px;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2">
				<strong><?php _e('Backup/Restore settings','leaflet-maps-marker'); ?> <a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" /></a></strong>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<p><?php _e('Below you find you current settings. Use copy and paste to make a backup or restore.','leaflet-maps-marker'); ?></p>
				<?php
					$settings_tinymce = array(
					'wpautop' => false,
					'media_buttons' => false,
					'tinymce' => array(
					 ),
					'quicktags' => false
					);
					wp_editor( $serialized_options, 'settings-array', $settings_tinymce);
					echo '<div style="margin:10px 0;"><strong><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') . '</a></strong></div>';
				?>
				<script type="text/javascript">
					(function($) {
						$("#settings-array").click(function(){
							this.select();
						});
					})(jQuery);
				</script>
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="deleted-maps-errors"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="deleted_maps_errors" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2"><strong><?php _e('Active shortcodes with invalid map IDs','leaflet-maps-marker') ?></strong></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;">
				<?php
					echo '<div style="margin:10px 0;"><strong><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') . '</a></strong></div>';
				?>				
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="move-markers"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="mass_assign" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2"><strong><?php _e('Move markers to a layer','leaflet-maps-marker') ?></strong></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;">
				<?php _e('Source','leaflet-maps-marker') ?>:
				<select id="layer_assign_from" name="layer_assign_from">
				<option value="0">ID 0 - <?php _e('unassigned','leaflet-maps-marker') ?></option>
				<?php
				foreach ($layerlist as $row) {
					if ($row['multi_layer_map'] == 0) {
						$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$row['id']);
						echo '<option value="' . intval($row['id']) . '">ID ' . intval($row['id']) . ' - ' . stripslashes(htmlspecialchars($row['name'])) . ' (' . $markercount .' ' . __('marker','leaflet-maps-marker') . ')</option>';
					} else {
						echo '<option value="' . intval($row['id']) . '" disabled="disabled">ID ' . intval($row['id']) . ' - ' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('This is a multi-layer map - markers cannot be assigned to this layer directly','leaflet-maps-marker') . ')</option>';
					}
				}
				?>
				</select>
				<br/>
				<?php _e('Target','leaflet-maps-marker') ?>:
				<select id="layer_assign_to" name="layer_assign_to">
				<option value="0">ID 0 - <?php _e('unassigned','leaflet-maps-marker') ?></option>
				<?php
				foreach ($layerlist as $row) {
					if ($row['multi_layer_map'] == 0) {
						$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$row['id']);
						echo '<option value="' . intval($row['id']) . '">ID ' . intval($row['id']) . ' - ' . stripslashes(htmlspecialchars($row['name'])) . ' (' . $markercount .' ' . __('marker','leaflet-maps-marker') . ')</option>';
					} else {
						echo '<option value="' . intval($row['id']) . '" disabled="disabled">ID ' . intval($row['id']) . ' - ' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('This is a multi-layer map - markers cannot be assigned to this layer directly','leaflet-maps-marker') . ')</option>';
					}
				}
				?>
				</select>
			</td>
			<td>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_asign-submit" value="<?php _e('move markers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to move the selected markers?','leaflet-maps-marker') ?>')" />
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="bulk-update-markers"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="3"><strong><?php echo __('Bulk updates for marker maps','leaflet-maps-marker'); ?></strong></td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="basemap" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Basemap','leaflet-maps-marker') ?></strong>
			</td>
			<td class="lmm-border">
				<input id="markermaps_osm_mapnik" type="radio" name="basemap" value="osm_mapnik" checked /> <label for="markermaps_osm_mapnik"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_osm_mapnik'])); ?></label><br />
				<input id="markermaps_googleLayer_roadmap" type="radio" name="basemap" value="googleLayer_roadmap" /> <label for="markermaps_googleLayer_roadmap"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_roadmap'])); ?></label><br />
				<input id="markermaps_googleLayer_satellite" type="radio" name="basemap" value="googleLayer_satellite" /> <label for="markermaps_googleLayer_satellite"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_satellite'])); ?></label><br />
				<input id="markermaps_googleLayer_hybrid" type="radio" name="basemap" value="googleLayer_hybrid" /> <label for="markermaps_googleLayer_hybrid"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_hybrid'])); ?></label><br />
				<input id="markermaps_googleLayer_terrain" type="radio" name="basemap" value="googleLayer_terrain" /> <label for="markermaps_googleLayer_terrain"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_terrain'])); ?></label><br />
				<input id="markermaps_bingaerial" type="radio" name="basemap" value="bingaerial" /> <label for="markermaps_bingaerial"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_bingaerial'])); ?></label><br />
				<input id="markermaps_bingaerialwithlabels" type="radio" name="basemap" value="bingaerialwithlabels" /> <label for="markermaps_bingaerialwithlabels"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_bingaerialwithlabels'])); ?></label><br />
				<input id="markermaps_bingroad" type="radio" name="basemap" value="bingroad" /> <label for="markermaps_bingroad"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_bingroad'])); ?></label><br />
				<input id="markermaps_ogdwien_basemap" type="radio" name="basemap" value="ogdwien_basemap" /> <label for="markermaps_ogdwien_basemap"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_ogdwien_basemap'])); ?></label><br />
				<input id="markermaps_ogdwien_satellite" type="radio" name="basemap" value="ogdwien_satellite" /> <label for="markermaps_ogdwien_satellite"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_ogdwien_satellite'])); ?></label><br />
				<input id="markermaps_mapbox" type="radio" name="basemap" value="mapbox" /> <label for="markermaps_mapbox"><?php echo htmlspecialchars(addslashes($lmm_options['mapbox_name'])); ?></label><br />
				<input id="markermaps_mapbox2" type="radio" name="basemap" value="mapbox2" /> <label for="markermaps_mapbox2"><?php echo htmlspecialchars(addslashes($lmm_options['mapbox2_name'])); ?></label><br />
				<input id="markermaps_mapbox3" type="radio" name="basemap" value="mapbox3" /> <label for="markermaps_mapbox3"><?php echo htmlspecialchars(addslashes($lmm_options['mapbox3_name'])); ?></label><br />
				<input id="markermaps_custom_basemap" type="radio" name="basemap" value="custom_basemap" /> <label for="markermaps_custom_basemap"><?php echo htmlspecialchars(addslashes($lmm_options['custom_basemap_name'])); ?></label><br />
				<input id="markermaps_custom_basemap2" type="radio" name="basemap" value="custom_basemap2" /> <label for="markermaps_custom_basemap2"><?php echo htmlspecialchars(addslashes($lmm_options['custom_basemap2_name'])); ?></label><br />
				<input id="markermaps_custom_basemap3" type="radio" name="basemap" value="custom_basemap3" /> <label for="markermaps_custom_basemap3"><?php echo htmlspecialchars(addslashes($lmm_options['custom_basemap3_name'])); ?></label>
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="basemap-submit" value="<?php _e('change basemap','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the basemap for selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="overlays" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Checked overlays in control box','leaflet-maps-marker') ?></strong>
			</td>
			<td class="lmm-border">
				<input id="markermaps_overlays_custom" type="checkbox" name="overlays_custom" /> <label for="markermaps_overlays_custom"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom_name'])); ?></label><br />
				<input id="markermaps_overlays_custom2" type="checkbox" name="overlays_custom2" /> <label for="markermaps_overlays_custom2"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom2_name'])); ?></label><br />
				<input id="markermaps_overlays_custom3" type="checkbox" name="overlays_custom3" /> <label for="markermaps_overlays_custom3"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom3_name'])); ?></label><br />
				<input id="markermaps_overlays_custom4" type="checkbox" name="overlays_custom4" /> <label for="markermaps_overlays_custom4"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom4_name'])); ?></label>
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="overlays-submit" value="<?php _e('change overlay status','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the overlay status for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="wms" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Active WMS layers','leaflet-maps-marker') ?></strong>
			</td>
			<td class="lmm-border">
				<input type="checkbox" name="wms" /> <?php echo wp_kses($lmm_options['wms_wms_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 1 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms1"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms2" /> <?php echo wp_kses($lmm_options['wms_wms2_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 2 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms2"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms3" /> <?php echo wp_kses($lmm_options['wms_wms3_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 3 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms3"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms4" /> <?php echo wp_kses($lmm_options['wms_wms4_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 4 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms4"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms5" /> <?php echo wp_kses($lmm_options['wms_wms5_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 5 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms5"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms6" /> <?php echo wp_kses($lmm_options['wms_wms6_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 6 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms6"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms7" /> <?php echo wp_kses($lmm_options['wms_wms7_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 7 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms7"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms8" /> <?php echo wp_kses($lmm_options['wms_wms8_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 8 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms8"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms9" /> <?php echo wp_kses($lmm_options['wms_wms9_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 9 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms9"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms10" /> <?php echo wp_kses($lmm_options['wms_wms10_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 10 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms10"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="wms-submit" value="<?php _e('change active WMS layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change active WMS layers for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="mapsize" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Map size','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:top;" class="lmm-border">
				<?php _e('Width','leaflet-maps-marker') ?>:
				<input size="2" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo intval($lmm_options[ 'defaults_marker_mapwidth' ]) ?>" style="margin-left:5px;" />
				<input id="markermaps_mapwidthunit_px" type="radio" name="mapwidthunit" value="px" checked /><label for="markermaps_mapwidthunit_px">px</label>&nbsp;&nbsp;&nbsp;
				<input id="markermaps_mapwidthunit_percent" type="radio" name="mapwidthunit" value="%" /><label for="markermaps_mapwidthunit_percent">%</label><br/>
				<?php _e('Height','leaflet-maps-marker') ?>:
				<input size="2" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo intval($lmm_options[ 'defaults_marker_mapheight' ]) ?>" /> px
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mapsize-submit" value="<?php _e('change mapsize','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the map size for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle;" class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="zoom" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Zoom','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:top;" class="lmm-border">
				<input style="width: 40px;" type="text" name="zoom" value="<?php echo intval($lmm_options[ 'defaults_marker_zoom' ]) ?>" />
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="zoom-submit" value="<?php _e('change zoom','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the zoom level for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="controlbox" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Basemap/overlay controlbox on frontend','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:top;" class="lmm-border">
				<input id="markermaps_controlbox_hidden" type="radio" name="controlbox" value="0" /><label for="markermaps_controlbox_hidden"><?php _e('hidden','leaflet-maps-marker') ?></label><br/>
				<input id="markermaps_controlbox_collapsed" type="radio" name="controlbox" value="1" checked /><label for="markermaps_controlbox_collapsed"><?php _e('collapsed (except on mobiles)','leaflet-maps-marker') ?></label><br/>
				<input id="markermaps_controlbox_expanded" type="radio" name="controlbox" value="2" /><label for="markermaps_controlbox_expanded"><?php _e('expanded','leaflet-maps-marker') ?></label><br/>
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="controlbox-submit" value="<?php _e('change controlbox status','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the controlbox status for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="panel" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Panel for displaying marker name and API URLs on top of map','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:top;" class="lmm-border">
				<input id="markermaps_panel_show" type="radio" name="panel" value="1" checked />
				<label for="markermaps_panel_show"><?php _e('show','leaflet-maps-marker') ?></label><br/>
				<input id="markermaps_panel_hide" type="radio" name="panel" value="0" />
				<label for="markermaps_panel_hide"><?php _e('hide','leaflet-maps-marker') ?></label></p></td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="panel-submit" value="<?php _e('change panel status','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the panel status for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="icon" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Icon','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="default_icon"><img src="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' ?>"/></label><br/>
				<input style="margin:1px 0 0 1px;" id="default_icon" type="radio" name="icon" value="" checked />
				</div>
				<?php
				$iconlist = array();
				$dir = opendir(LEAFLET_PLUGIN_ICONS_DIR);
				while ($file = readdir($dir)) {
					if ($file === false) {
						break;
					}
					if ($file != "." and $file != "..") {
						$path_info = pathinfo($file);
						if (!is_dir($dir.$file) && ((strtolower($path_info['extension']) == 'png') || (strtolower($path_info['extension']) == 'jpg') || (strtolower($path_info['extension']) == 'gif')) ) {
							$iconlist[] = $file;
						}
					}
				}
				closedir($dir);
				sort($iconlist);
				foreach ($iconlist as $row) {
				  $icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
				  if ($icon_data == NULL) { //info: workaround #1 due to support request
						$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
				  }
				  if ($icon_data == NULL) { //info: workaround #2 due to support request
						$icon_data = wp_remote_get(LEAFLET_PLUGIN_ICONS_URL . '/' . $row, array('sslverify' => false, 'timeout' => 1));
				  }
				  $icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
 				  echo '<div style="text-align:center;float:left;line-height:0;margin-bottom:3px;"><label for="'.$row.'"><img id="iconpreview" src="' . $icon_base64 . '" title="' . $row . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input style="margin:1px 0 0 1px;" id="' . $row . '" type="radio" name="icon" value="' . $row . '"/></div>';
				}
				?>
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="icon-submit" value="<?php _e('update icon','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the icon for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="openpopup" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Popup status','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:top;" class="lmm-border">
				<input id="markermaps_openpopup_closed" type="radio" name="openpopup" value="0" checked />
				<label for="markermaps_openpopup_closed"><?php _e('closed','leaflet-maps-marker') ?></label>&nbsp;&nbsp;&nbsp;
				<input id="markermaps_openpopup_open" type="radio" name="openpopup" value="1" />
				<label for="markermaps_openpopup_open"><?php _e('open','leaflet-maps-marker') ?></label></td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="openpopup-submit" value="<?php _e('change popup status','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the popup status for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="popuptext" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Popup text','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<?php
					global $wp_version;
					if ( version_compare( $wp_version, '3.9-alpha', '>=' ) ) {
						$settings = array(
							'wpautop' => true,
							'tinymce' => array(
								'height' => '250',
								'content_style' => 'img {' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '} a {text-decoration:none;} a:hover {text-decoration:underline;}'
							 ),
						'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,code,close'));
					} else {
						$settings = array(
							'wpautop' => true,
							'tinymce' => array(
								'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,link,unlink,|,ltr,rtl',
								'theme' => 'advanced',
								'height' => '250',
								'content_style' => 'html .mcecontentbody {font:12px/1.4 "Helvetica Neue",Arial,Helvetica,sans-serif; max-width:' . (intval($lmm_options['defaults_marker_popups_maxwidth']) + 1) . 'px; /* Default + 1 fix */ word-wrap:break-word;} .mcecontentbody a {text-decoration:none;} .mcecontentbody a:hover {text-decoration:underline;} .mcecontentbody img {' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '}',
								'theme_advanced_statusbar_location' => 'bottom'
							),
						'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,code,close'));
					}
					wp_editor( '', 'popuptext', $settings);
				?>
			</td>
			<td style="vertical-align:middle;text-align:center;" class="lmm-border">
				<?php _e('Which markers should be updated?','leaflet-maps-marker'); ?>
				<select id="marker-basemap-layer" name="marker-basemap-layer" style="width:230px;">
					<option value="all">
					<?php echo sprintf(__('all %1$s markers','leaflet-maps-marker'), $markercount_all) ?>
					</option>
					<option value="0" disabled="disabled">
					<?php _e('markers not assigned to a layer','leaflet-maps-marker') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 21) ? '...': '';
							echo '<option value="' . $row['id'] . '" title="' . sprintf(esc_attr__('Update markers from the following layer only: "%1$s"','leaflet-maps-marker'), stripslashes(htmlspecialchars($row['name']))) . '" disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						}
					?>
				</select><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><?php _e('upgrade to pro for updating markers from selected layers only','leaflet-maps-marker'); ?></a></small><br/><br/>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="popuptext-submit" value="<?php _e('change popup text','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the popup text for the selected markers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
	</table>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="bulk-update-layers"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="3"><strong><?php echo sprintf( esc_attr__('Bulk updates for all %1$s existing layer maps','leaflet-maps-marker'), $layercount_all) ?></strong></td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="basemap-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Basemap','leaflet-maps-marker') ?></strong>
			</td>
			<td class="lmm-border">
				<input id="layermaps_osm_mapnik" type="radio" name="basemap-layer" value="osm_mapnik" checked /> <label for="layermaps_osm_mapnik"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_osm_mapnik'])); ?></label><br />
				<input id="layermaps_googleLayer_roadmap" type="radio" name="basemap-layer" value="googleLayer_roadmap" /> <label for="layermaps_googleLayer_roadmap"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_roadmap'])); ?></label><br />
				<input id="layermaps_googleLayer_satellite" type="radio" name="basemap-layer" value="googleLayer_satellite" /> <label for="layermaps_googleLayer_satellite"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_satellite'])); ?></label><br />
				<input id="layermaps_googleLayer_hybrid" type="radio" name="basemap-layer" value="googleLayer_hybrid" /> <label for="layermaps_googleLayer_hybrid"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_hybrid'])); ?></label><br />
				<input id="layermaps_googleLayer_terrain" type="radio" name="basemap-layer" value="googleLayer_terrain" /> <label for="layermaps_googleLayer_terrain"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_googleLayer_terrain'])); ?></label><br />
				<input id="layermaps_bingaerial" type="radio" name="basemap-layer" value="bingaerial" /> <label for="layermaps_bingaerial"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_bingaerial'])); ?></label><br />
				<input id="layermaps_bingaerialwithlabels" type="radio" name="basemap-layer" value="bingaerialwithlabels" /> <label for="layermaps_bingaerialwithlabels"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_bingaerialwithlabels'])); ?></label><br />
				<input id="layermaps_bingroad" type="radio" name="basemap-layer" value="bingroad" /> <label for="layermaps_bingroad"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_bingroad'])); ?></label><br />
				<input id="layermaps_ogdwien_basemap" type="radio" name="basemap-layer" value="ogdwien_basemap" /> <label for="layermaps_ogdwien_basemap"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_ogdwien_basemap'])); ?></label><br />
				<input id="layermaps_ogdwien_satellite" type="radio" name="basemap-layer" value="ogdwien_satellite" /> <label for="layermaps_ogdwien_satellite"><?php echo htmlspecialchars(addslashes($lmm_options['default_basemap_name_ogdwien_satellite'])); ?></label><br />
				<input id="layermaps_mapbox" type="radio" name="basemap-layer" value="mapbox" /> <label for="layermaps_mapbox"><?php echo htmlspecialchars(addslashes($lmm_options['mapbox_name'])); ?></label><br />
				<input id="layermaps_mapbox2" type="radio" name="basemap-layer" value="mapbox2" /> <label for="layermaps_mapbox2"><?php echo htmlspecialchars(addslashes($lmm_options['mapbox2_name'])); ?></label><br />
				<input id="layermaps_mapbox3" type="radio" name="basemap-layer" value="mapbox3" /> <label for="layermaps_mapbox3"><?php echo htmlspecialchars(addslashes($lmm_options['mapbox3_name'])); ?></label><br />
				<input id="layermaps_custom_basemap" type="radio" name="basemap-layer" value="custom_basemap" /> <label for="layermaps_custom_basemap"><?php echo htmlspecialchars(addslashes($lmm_options['custom_basemap_name'])); ?></label><br />
				<input id="layermaps_custom_basemap2" type="radio" name="basemap-layer" value="custom_basemap2" /> <label for="layermaps_custom_basemap2"><?php echo htmlspecialchars(addslashes($lmm_options['custom_basemap2_name'])); ?></label><br />
				<input id="layermaps_custom_basemap3" type="radio" name="basemap-layer" value="custom_basemap3" /> <label for="layermaps_custom_basemap3"><?php echo htmlspecialchars(addslashes($lmm_options['custom_basemap3_name'])); ?></label>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="basemap-layer-submit" value="<?php _e('change basemap for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the basemap for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="overlays-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Checked overlays in control box','leaflet-maps-marker') ?></strong>
			</td>
			<td class="lmm-border">
				<input id="layermaps_overlays_custom-layer" type="checkbox" name="overlays_custom-layer" /> <label for="layermaps_overlays_custom-layer"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom_name'])); ?></label><br />
				<input id="layermaps_overlays_custom-layer2" type="checkbox" name="overlays_custom2-layer" /> <label for="layermaps_overlays_custom-layer2"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom2_name'])); ?></label><br />
				<input id="layermaps_overlays_custom-layer3" type="checkbox" name="overlays_custom3-layer" /> <label for="layermaps_overlays_custom-layer3"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom3_name'])); ?></label><br />
				<input id="layermaps_overlays_custom-layer4" type="checkbox" name="overlays_custom4-layer" /> <label for="layermaps_overlays_custom-layer4"><?php echo htmlspecialchars(addslashes($lmm_options['overlays_custom4_name'])); ?></label>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="overlays-layer-submit" value="<?php _e('change overlay status for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the overlay status for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="wms-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Active WMS layers','leaflet-maps-marker') ?></strong>
			</td>
			<td class="lmm-border">
				<input type="checkbox" name="wms-layer" /> <?php echo wp_kses($lmm_options['wms_wms_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 1 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms1"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms2-layer" /> <?php echo wp_kses($lmm_options['wms_wms2_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 2 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms2"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms3-layer" /> <?php echo wp_kses($lmm_options['wms_wms3_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 3 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms3"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms4-layer" /> <?php echo wp_kses($lmm_options['wms_wms4_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 4 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms4"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms5-layer" /> <?php echo wp_kses($lmm_options['wms_wms5_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 5 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms5"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms6-layer" /> <?php echo wp_kses($lmm_options['wms_wms6_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 6 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms6"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms7-layer" /> <?php echo wp_kses($lmm_options['wms_wms7_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 7 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms7"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms8-layer" /> <?php echo wp_kses($lmm_options['wms_wms8_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 8 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms8"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms9-layer" /> <?php echo wp_kses($lmm_options['wms_wms9_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 9 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms9"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br />
				<input type="checkbox" name="wms10-layer" /> <?php echo wp_kses($lmm_options['wms_wms10_name'], $allowedtags); ?> <a title="<?php esc_attr_e('WMS layer 10 settings','leaflet-maps-marker'); ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_settings#lmm-wms-wms10"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="wms-layer-submit" value="<?php _e('change active WMS layers for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change active WMS layers for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="mapsize-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Map size','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<?php _e('Width','leaflet-maps-marker') ?>:
				<input size="2" maxlength="4" type="text" id="mapwidth-layer" name="mapwidth-layer" value="<?php echo intval($lmm_options[ 'defaults_layer_mapwidth' ]) ?>" style="margin-left:5px;" />
				<input id="layermaps_mapwidthunit_px" type="radio" name="mapwidthunit-layer" value="px" checked /><label for="layermaps_mapwidthunit_px">px</label>&nbsp;&nbsp;&nbsp;
				<input id="layermaps_mapwidthunit_percent" type="radio" name="mapwidthunit-layer" value="%" /><label for="layermaps_mapwidthunit_percent">%</label><br/>
				<?php _e('Height','leaflet-maps-marker') ?>:
				<input size="2" maxlength="4" type="text" id="mapheight-layer" name="mapheight-layer" value="<?php echo intval($lmm_options[ 'defaults_layer_mapheight' ]) ?>" /> px
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mapsize-layer-submit" value="<?php _e('change mapsize for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the map size for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle;" class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="zoom-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Zoom','leaflet-maps-marker') ?></strong>
			</td>
				<td style="vertical-align:middle;" class="lmm-border">
				<input style="width: 40px;" type="text" id="zoom-layer" name="zoom-layer" value="<?php echo intval($lmm_options[ 'defaults_layer_zoom' ]) ?>" />
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="zoom-layer-submit" value="<?php _e('change zoom for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the zoom level for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="controlbox-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Basemap/overlay controlbox on frontend','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input id="layermaps_controlbox_hidden" type="radio" name="controlbox-layer" value="0" /><label for="layermaps_controlbox_hidden"><?php _e('hidden','leaflet-maps-marker') ?></label><br/>
				<input id="layermaps_controlbox_collapsed" type="radio" name="controlbox-layer" value="1" checked /><label for="layermaps_controlbox_collapsed"><?php _e('collapsed (except on mobiles)','leaflet-maps-marker') ?></label><br/>
				<input id="layermaps_controlbox_expanded" type="radio" name="controlbox-layer" value="2" /><label for="layermaps_controlbox_expanded"><?php _e('expanded','leaflet-maps-marker') ?></label><br/>
				</td>
				<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="controlbox-layer-submit" value="<?php _e('change controlbox status for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the controlbox status for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="panel-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Panel for displaying layer name and API URLs on top of map','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input id="layermaps_panel_show" type="radio" name="panel-layer" value="1" checked />
				<label for="layermaps_panel_show"><?php _e('show','leaflet-maps-marker') ?></label><br/>
				<input id="layermaps_panel_hide" type="radio" name="panel-layer" value="0" />
				<label for="layermaps_panel_hide"><?php _e('hide','leaflet-maps-marker') ?></label></p></td>
				<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="panel-layer-submit" value="<?php _e('change panel status for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the panel status for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<input type="hidden" name="action" value="listmarkers-layer" />
				<?php wp_nonce_field('tool-nonce'); ?>
				<strong><?php _e('Display a list of markers below the map','leaflet-maps-marker') ?></strong>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input id="layermaps_listmarkers_yes" type="radio" name="listmarkers-layer" value="1" checked />
				<label for="layermaps_listmarkers_yes"><?php _e('yes','leaflet-maps-marker') ?></label><br/>
				<input id="layermaps_listmarkers_no" type="radio" name="listmarkers-layer" value="0" />
				<label for="layermaps_listmarkers_no"><?php _e('no','leaflet-maps-marker') ?></label></p></td>
				<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="listmarkers-layer-submit" value="<?php _e('change list marker-status for all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to change the list marker-status for all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
				</form>
			</td>
		</tr>
		<tr>
			<td class="lmm-border">
				<form method="post">
				<strong><?php _e('Marker clustering','leaflet-maps-marker') ?></strong><br/><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" /></a>
			</td>
			<td style="vertical-align:middle;" class="lmm-border">
				<input id="layermaps_clustering_enabled" type="radio" name="listmarkers-clustering" value="1" disabled="disabled" />
				<label for="layermaps_clustering_enabled"><?php _e('enabled','leaflet-maps-marker') ?></label><br/>
				<input id="layermaps_clustering_disabled" type="radio" name="listmarkers-clustering" value="0" checked disabled="disabled" />
				<label for="layermaps_listmarkers_disabled"><?php _e('disabled','leaflet-maps-marker') ?></label></p></td>
				<td style="vertical-align:middle;" class="lmm-border">
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="listmarkers-clustering-submit" value="<?php _e('change clustering status for all layers','leaflet-maps-marker') ?> &raquo;" disabled="disabled" />
				</form>
			</td>
		</tr>
	</table>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="change-marker-id"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="change_marker_id" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2"><strong><?php _e('Change marker ID','leaflet-maps-marker') ?> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" /></strong></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;">
				<?php _e('Old marker ID','leaflet-maps-marker') ?>:
				<input id="marker_id_old" name="marker_id_old" type="text" size="4" />
				<?php _e('New marker ID','leaflet-maps-marker') ?>:
				<input id="marker_id_new" name="marker_id_new" type="text" size="4" />
			</td>
		</tr>
		<tr>
			<td>
				<?php echo '<strong><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') . '</a></strong>'; ?>
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="change-layer-id"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="change_layer_id" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td><strong><?php _e('Change layer ID','leaflet-maps-marker') ?> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" /></strong></td>
		</tr>
		<tr>
			<td>
				<p><?php _e('Markers assigned to this layer and multi-layer-maps including this layer will also be updated. Please keep in mind that posts, pages, widgets or template files using a Maps Marker Pro shortcode with the old layer ID have to be updated manually!','leaflet-maps-marker'); ?></p>
				<?php _e('Old layer ID','leaflet-maps-marker') ?>:
				<input id="layer_id_old" name="layer_id_old" type="text" size="4" />
				<?php _e('New layer ID','leaflet-maps-marker') ?>:
				<input id="layer_id_new" name="layer_id_new" type="text" size="4" />
				<?php echo '<strong><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') . '</a></strong>'; ?>
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="delete-selected-markers"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="mass_delete_from_layer" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2"><strong><?php _e('Delete all markers from a layer','leaflet-maps-marker') ?></strong></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;">
				<?php _e('Layer','leaflet-maps-marker') ?>:
				<select id="delete_from_layer" name="delete_from_layer">
				<option value="0">ID 0 - <?php _e('unassigned','leaflet-maps-marker') ?></option>
				<?php
				foreach ($layerlist as $row) {
					$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$row['id']);
					echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes(htmlspecialchars($row['name'])) . ' (' . $markercount .' ' . __('marker','leaflet-maps-marker') . ')</option>';
				}
				?>
				</select>
			</td>
			<td>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_delete_from_layer-submit" value="<?php _e('delete all markers from selected layer','leaflet-maps-marker') ?> &raquo;" onclick="return 	confirm('<?php _e('Do you really want to delete all markers from the selected layer? (cannot be undone)','leaflet-maps-marker') ?>')" />
			</td>
		</tr>
	</table>
	</form>	
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>

	<a name="delete-all-markers"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="mass_delete_all_markers" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<?php
			$delete_all = sprintf( esc_attr__('Delete all %1$s markers from all %2$s layers','leaflet-maps-marker'), $markercount_all, $layercount_all);
			?>
			<td colspan="2"><strong><?php echo $delete_all ?></strong></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;">
				<input id="delete_all_markers_from_all_layers" type="checkbox" id="delete_confirm_checkbox" name="delete_confirm_checkbox" /> <label for="delete_all_markers_from_all_layers"><?php _e('Yes','leaflet-maps-marker') ?></label>
			</td>
			<td>
				<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_delete_all_markers" value="<?php _e('delete all markers from all layers','leaflet-maps-marker') ?> &raquo;" onclick="return confirm('<?php esc_attr_e('Do you really want to delete all markers from all layers? (cannot be undone)','leaflet-maps-marker') ?>')" />
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>
	
	<a name="marker-validity-check"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="marker_validity_check" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2"><strong><?php echo __('Marker validity check for layer assignements','leaflet-maps-marker'); ?></strong> <a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" /></a></td>
		</tr>
		<tr>
			<td>
				<?php _e('Check if any markers exist that are assigned to layers that do not exist (anymore). This can happen if you deleted a layer but did not update the assignement of the related markers.','leaflet-maps-marker'); ?><br/>

			<?php echo '<div style="margin:10px 0;"><strong><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') . '</a></strong></div>'; ?>
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>
    
    
    	<a name="initialize-map-texts-wpml"></a>
	<br/><br/>
	<?php $nonce= wp_create_nonce('tool-nonce'); ?>
	<form method="post">
	<input type="hidden" name="action" value="prepare_strings_wpml" />
	<?php wp_nonce_field('tool-nonce'); ?>
	<table class="widefat fixed" style="width:auto;">
		<tr style="background-color:#d6d5d5;">
			<td colspan="2"><strong><?php echo __('Initialize map texts for translation','leaflet-maps-marker'); ?></strong>  <a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" /></a></td>
		</tr>
		<tr>
			<td>
				<?php 
					echo sprintf(__('Prepare existing maps strings (marker name, marker address, marker popuptext, layer name, layer address) for translation using the <a href="%1$s">%2$s plugin</a>.','leaflet-maps-marker'), 'https://www.mapsmarker.com/multilingual', 'WPML string translation or Polylang'); 
					echo '<div style="margin:10px 0;"><strong><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','leaflet-maps-marker') . '</a></strong></div>'; ?>
			</td>
		</tr>
	</table>
	</form>
	<p><a href="#top" style="text-decoration:none;"><?php _e('back to top','leaflet-maps-marker'); ?></a></p>
    
	</div>
	<!--wrap-->
<?php } include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php'); ?>