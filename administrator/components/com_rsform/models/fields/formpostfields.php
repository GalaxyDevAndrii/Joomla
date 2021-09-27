<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

class JFormFieldFormpostfields extends JFormField
{
	protected function getInput()
	{
		$button = '<p><button type="button" onclick="RSFormPro.Post.addField();" class="btn btn-primary">' . JText::_('RSFP_ADD_POST_VALUE') .  '</button></p>';

		$table = '
<div>
	<table class="com-rsform-table-props table table-striped table-hover" id="com-rsform-post-fields">
	<thead>
		<tr>
			<th>' . JText::_('RSFP_POST_NAME') . '</th>
			<th colspan="2">' . JText::_('RSFP_POST_VALUE') . '</th>
		</tr>
		</thead>
		<tbody>';

		if (is_array($this->value))
		{
			$i = 0;

			foreach ($this->value as $field)
			{
				$table .= '
<tr>
	<td><input type="text" class="rs_inp rs_80" name="form_post[name][]" id="form_post_name' . $i . '" placeholder="' . $this->escape(JText::_('RSFP_POST_NAME_PLACEHOLDER')) . '" data-delimiter=" " data-placeholders="display" value="' . $this->escape($field->name) . '" /></td>
	<td><input type="text" class="rs_inp rs_80" name="form_post[value][]" id="form_post_value' . $i . '" placeholder="' . $this->escape(JText::_('RSFP_POST_VALUE_PLACEHOLDER')) . '" data-delimiter=" " data-placeholders="display" data-filter-type="include" data-filter="value,global" value="' . $this->escape($field->value) . '" /></td>
	<td><button type="button" onclick="RSFormPro.Post.deleteField.call(this);" class="btn btn-danger btn-mini"><i class="rsficon rsficon-remove"></i></button></td>
</tr>';
			$i++;
			}
		}

		$table .= '
		</tbody>
	</table>
</div>';

		return $button . $table;
	}

	protected function escape($string)
	{
		return htmlspecialchars($string, ENT_COMPAT, 'utf-8');
	}
}
