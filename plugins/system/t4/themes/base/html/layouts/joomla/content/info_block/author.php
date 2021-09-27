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
// load language t4
\JFactory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
?>
<dd class="createdby" itemprop="author" itemscope itemtype="https://schema.org/Person">
	<?php $author = ($displayData['item']->created_by_alias ?: $displayData['item']->author); ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php if (!empty($displayData['item']->contact_link ) && $displayData['params']->get('link_author') == true) : ?>
		<?php echo Text::sprintf('TPL_CONTENT_WRITTEN_BY', HTMLHelper::_('link', $displayData['item']->contact_link, $author, array('itemprop' => 'url'))); ?>
	<?php else : ?>
		<?php echo Text::sprintf('TPL_CONTENT_WRITTEN_BY', $author); ?>
	<?php endif; ?>
</dd>

<span style="display: none;" itemprop="publisher" itemtype="http://schema.org/Organization" itemscope>
	<?php $author = ($displayData['item']->created_by_alias ?: $displayData['item']->author); ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php echo $author; ?>
</span>
