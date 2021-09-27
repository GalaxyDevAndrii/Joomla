<?php
$doc = $displayData->doc;
$logoFile = $doc->params->get('logoFile', 'none');
?>
<a class="navbar-brand t4-logo" href="index.php"><img src="<?php echo $logoFile ?>" alt="T4 Blank" /></a>