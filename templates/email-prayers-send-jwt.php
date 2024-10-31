<?php 
// build the token url
$token_url = get_site_url() . '/prayers/manage?token=' . $token;
?>

Please click the following link to manage your prayer requests:  <?php echo $token_url; ?>