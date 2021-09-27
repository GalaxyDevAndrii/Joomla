<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen', '.advancedSelect');
?>
<fieldset class="form-horizontal">
	<?php
		echo $this->form->renderFieldset('permissions');
	?>
</fieldset>