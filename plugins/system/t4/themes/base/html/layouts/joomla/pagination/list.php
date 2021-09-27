<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;

$list = $displayData['list'];

?>
<nav role="navigation" aria-label="<?php echo Text::_('JLIB_HTML_PAGINATION'); ?>">
	<ul class="pagination">
		<li class="page-item"><?php echo $list['start']['data']; ?></li>
		<li class="page-item"><?php echo $list['previous']['data']; ?></li>

		<?php foreach ($list['pages'] as $page) : ?>
			<li class="page-item"><?php echo $page['data']; ?></li>
		<?php endforeach; ?>

		<li class="page-item"><?php echo $list['next']['data']; ?></li>
		<li class="page-item"><?php echo $list['end']['data']; ?></li>
	</ul>
</nav>
