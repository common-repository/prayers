<div id="prayer-responses" class="inside hidden prayer-section">

	<?php $response = get_post_meta( get_the_ID(), 'prayer-response', true ); ?>
	<textarea name="prayer-response" placeholder="How was this prayer answered?" class="prayer-response-textarea"><?php
		echo $response;
	?></textarea>

</div>
