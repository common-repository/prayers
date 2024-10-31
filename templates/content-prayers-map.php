<?php
	/**
	 * Prayer Map Options
	 */

	if ( ! is_null( $px_height) ) { $pxh = "height: {$px_height}px; "; }
	if ( ! is_null( $px_width) ) { $pxw = "width: {$px_width}px; "; }
	if ( ! is_null( $pct_height) ) { $pcth = "height: {$pct_height}%; "; }
	if ( ! is_null( $pct_width) ) { $pctw = "height: {$pct_width}%; "; }

	$inline = $pxh . $pxw . $pcth . $pctw;

	// prayer navigation
	Prayer_Template_Helper::get_navigation();

?><div id="prayer-map" class="map prayer-map prayer-js" style="<?php echo $inline ?>"></div>
