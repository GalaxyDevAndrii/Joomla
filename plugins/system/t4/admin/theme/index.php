<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors.none
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use T4\Document\Edit as T4Edit;
/** @var JDocumentHtml $this */

$app  = JFactory::getApplication();
$user = JFactory::getUser();

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{t4:language}" lang="{t4:language}" dir="{t4:direction}">

<head>
  <!--[if lt IE 9]>
    <script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
  <![endif]-->
  <meta name="viewport"  content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes" />
  <style type="text/css">
    @-webkit-viewport { width: device-width; } @-moz-viewport { width: device-width; } @-ms-viewport { width: device-width; } @-o-viewport { width: device-width; } @viewport { width: device-width; }
  </style>
  <meta name="HandheldFriendly" content="true" />
  <meta name="apple-mobile-web-app-capable" content="YES" />
  <jdoc:include type="head" />
  <link rel="stylesheet" href="<?php echo T4PATH_ADMIN_URI ?>/theme/css/style.css" />
  <link rel="stylesheet" href="<?php echo T4PATH_BASE_URI ?>/vendors/font-awesome/css/font-awesome.min.css" />
  {t4post:EditCss}
  <script type="text/javascript" src="<?php echo T4PATH_BASE_URI ?>/js/frontend-edit.js"></script>
  <script type="text/javascript" src="<?php echo T4PATH_ADMIN_URI ?>/theme/js/script.js"></script>
</head>

<body class="{t4post:bodyclass} t4-edit-layout">
  <!-- Header -->
  <header class="t4-header">
    <div class="container">
      <span class="brand">
        {t4post:logoedit}
      </span>
    </div>
  </header>
  <!-- // Header -->

  <!-- Main body -->
  <div class="t4-mainbody">
    <div class="container">
      <jdoc:include type="message" />
      <jdoc:include type="component" />
    </div>
  </div>
  <!-- // Main body -->

  <!-- Footer -->
  <footer class="t4-footer">
    <div class="container">
      <p>Copyright &copy; <?php echo date("Y"); ?> <?php echo JFactory::getConfig()->get('sitename');?>. All Rights Reserved</p>
    </div>
  </footer>
  <!-- // Footer -->
</body>

</html>
