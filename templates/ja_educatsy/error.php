<?php
/**
 *------------------------------------------------------------------------------
 * @package       JA Educatsy - Joomla LMS template for education and online learning
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 *------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
if (!isset($this->error)) {
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}
//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
$theme = JFactory::getApplication()->getTemplate(true)->params->get('theme', '');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet"> 

	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/error.css" type="text/css" />

</head>
<body class="page-error">
	<div class="main">
		<div class="error">
			<div id="outline">
				<div id="errorboxoutline">
					<div class="error-code"><?php 
						$errcode = str_split($this->error->getCode());
						$i = 0;
						$lastclass='';
						foreach($errcode as $c){
	                        $firstclass = ($i==0)?'first':'';
							if($i==(count($errcode)-1)){
								$lastclass='last';
							}
							echo '<span class="'.$lastclass.$firstclass.'">'.$c.'</span>';
							$i++;
						}
						?>
					</div>
					<div class="wrap-text">
						<div class="error-message"><h2><span><?php echo $this->error->getMessage(); ?></span></h2></div>
						<div id="errorboxbody">
							<p><?php echo JText::_('JERROR_LAYOUT_PLEASE_TRY_ONE_OF_THE_FOLLOWING_PAGES'); ?></p>
						</div>
						<a class="button-home" href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>"><?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?><span class="ion-android-arrow-forward"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
