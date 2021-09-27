<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<dd class="published">
	<span class="fa fa-calendar" aria-hidden="true"></span>
	<time datetime="<?php echo HTMLHelper::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
		<?php echo Text::sprintf(HTMLHelper::_('date', $displayData['item']->publish_up, Text::_('F j,Y'))); ?>
	</time>
</dd>
