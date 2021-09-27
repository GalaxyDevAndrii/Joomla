<?php 
/**
 * ------------------------------------------------------------------------
 * JA Educatsy Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
	$count = $helper->getRows('link');
?>

<div class="social-follow-wrap">
  <div class="social-follow d-flex">
  	<?php for ($i=0; $i<$count; $i++) : ?>
    	<div class="social-inner">
    		<a href="<?php echo $helper->get('link', $i) ;?>" title="<?php echo $helper->get('title', $i) ;?>">
          <span class="<?php echo $helper->get('font-icon', $i) ;?>"></span>
    			<span class="d-none"><?php echo $helper->get('title', $i) ;?></span>
    		</a>
    	</div>
    <?php endfor ?>
  </div>
</div>
