<ul id="prayer-navigation" class="prayer-navigation"><?php
	$output = array();
    foreach ( $links as $link ): ?>
    	<li class="<?php echo $link['class'] ?>"><a href="<?php echo $link['href'] ?>"><?php echo $link['title'] ?></a></li>
    <?php endforeach;
?></ul>
