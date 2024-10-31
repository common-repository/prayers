	<div class="wrap">
	<h2>Prayer Feeds</h2>

	<div id="col-container">

		<div id="col-right">
			<div class="col-wrap">

    			<h3>RSS Feeds</h3>

    			<table>
	    			<thead>
		    			<tr>
		    				<td><strong>Type</strong></td>
		    				<td><strong>Feed</strong></td>
		    			</tr>
	    			</thead>
	    			<tbody>
		    			<tr>
			    			<td>By Most Recent</td>
			    			<td><a target="_blank" href="<?php echo get_site_url(); ?>/feed?post_type=prayer"><?php echo get_site_url(); ?>/feed?post_type=prayer</a></td>
		    			</tr>
		    			<tr>
			    			<td>By Category</td>
			    			<td><a target="_blank" href="<?php echo get_site_url(); ?>/feed?post_type=prayer&amp;prayer-category=missions"><?php echo get_site_url(); ?>/feed?post_type=prayer&amp;prayer-category=missions</a></td>
		    			</tr>
		    			<tr>
			    			<td>By Tags</td>
			    			<td><a target="_blank" href="<?php echo get_site_url(); ?>/feed?post_type=prayer&amp;prayer-tag=africa,north-america"><?php echo get_site_url(); ?>/feed?post_type=prayer&amp;prayer-tag=africa,north-america</a></td>
		    			</tr>
	    			</tbody>
    			</table>

    			<h3>JSON Feeds</h3>

    			<table>
    				<thead>
	    				<tr>
	    					<td><strong>Type</strong></td>
	    					<td><strong>Feed</strong></td>
	    				</tr>
    				</thead>
    				<tbody>
    					<tr>
    						<td>By Most Recent</td>
    						<td>
    							<a href="<?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers" target="_blank"><?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers</a>
    						</td>
    					</tr>
						<tr>
					    	<td>By Answered</td>
					    	<td>
    							<a href="<?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?answered=1" target="_blank"><?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?answered=1</a>
    						</td>
					    </tr>
					    <tr>
					    	<td>By Country</td>
					    	<td>
    							<a href="<?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?country=us" target="_blank"><?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?country=us</a>
    						</td>
					    </tr>
					    <tr>
					    	<td>By Category</td>
					    	<td>
    							<a href="<?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?category=missions" target="_blank"><?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?category=missions</a>
    						</td>
						</tr>
						<tr>
					    	<td>By Tags</td>
					    	<td>
    							<a href="<?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?tags=africa,north-america" target="_blank"><?php echo get_site_url(); ?>/wp-json/prayers/v1/prayers?tags=africa,north-america</a>
    						</td>
						</tr>
					</tbody>
		    	</table>

			</div>
		</div>

		<div id="col-left">
    		<div class="col-wrap">

    			<p>Prayer provides both RSS and JSON feeds for your prayer requests. This gives you the ability to allow subscribers to subsribe to RSS or integrate the prayers on your website into other third-party services.</p>

    			<p>These feeds only display published prayers.</p>

    		</div>
		</div>

	</div>

</div>