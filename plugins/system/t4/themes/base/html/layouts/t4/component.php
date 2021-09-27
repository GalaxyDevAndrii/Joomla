<?php
/**
 * @package   T4_Blank
 * @copyright Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo !empty($this->language) ? $this->language : 'en-GB'; ?>" lang="<?php echo !empty($this->language) ? $this->language : 'en-GB';  ?>" dir="<?php echo !empty($this->direction) ? $this->direction : 'ltr'; ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
{t4post:head}
<!--[if lt IE 9]>
    <script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
<![endif]-->
</head>
<body class="contentpane {t4post:bodyclass}">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
