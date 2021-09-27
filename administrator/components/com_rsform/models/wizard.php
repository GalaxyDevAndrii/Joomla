<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelWizard extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.wizard', 'wizard', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Defaults
		return array(
			'AdminEmailTo'      => JFactory::getUser()->get('email'),
			'FormLayoutName'    => RSFormProHelper::getConfig('global.default_layout'),
			'Thankyou'          => JText::_('RSFP_THANKYOU_DEFAULT')
		);
	}

	public function addFields($row, $predefinedForm, $data)
	{
		$formId = $row->FormId;
		$db     = $this->getDbo();

		switch ($predefinedForm)
		{
			case 'simple':
				$fields = array(
					array(
						'NAME'              => 'Name',
						'CAPTION'           => 'Your Name',
						'REQUIRED'          => 'YES',
						'SIZE'              => '20',
						'VALIDATIONRULE'    => 'none',
						'VALIDATIONMESSAGE' => 'Please let us know your name.',
						'COMPONENTTYPE'     => RSFORM_FIELD_TEXTBOX
					),
					array(
						'NAME'              => 'Email',
						'CAPTION'           => 'Your Email',
						'REQUIRED'          => 'YES',
						'SIZE'              => '20',
						'VALIDATIONRULE'    => 'email',
						'VALIDATIONMESSAGE' => 'Please let us know your email address.',
						'COMPONENTTYPE'     => RSFORM_FIELD_TEXTBOX
					),
					array(
						'NAME'              => 'Subject',
						'CAPTION'           => 'Subject',
						'REQUIRED'          => 'YES',
						'SIZE'              => '20',
						'VALIDATIONRULE'    => 'none',
						'VALIDATIONMESSAGE' => 'Please write a subject for your message.',
						'COMPONENTTYPE'     => RSFORM_FIELD_TEXTBOX
					),
					array(
						'NAME'              => 'Message',
						'CAPTION'           => 'Message',
						'REQUIRED'          => 'YES',
						'COLS'              => '50',
						'ROWS'              => '5',
						'VALIDATIONRULE'    => 'none',
						'VALIDATIONMESSAGE' => 'Please let us know your message.',
						'COMPONENTTYPE'     => RSFORM_FIELD_TEXTAREA
					),
					array(
						'NAME'              => 'Send',
						'CAPTION'           => '',
						'LABEL'             => 'Send',
						'RESET'             => 'NO',
						'BUTTONTYPE'        => 'TYPEBUTTON',
						'COMPONENTTYPE'     => RSFORM_FIELD_SUBMITBUTTON
					)
				);
				break;

			case 'calculations':
				$fields = array(
					array(
						'NAME'              => 'Phone Title',
						'TEXT'              => '<h3>Personalize Your Phone</h3>',
						'COMPONENTTYPE'     => RSFORM_FIELD_FREETEXT
					),
					array(
						'NAME'              => 'Storage',
						'CAPTION'           => 'Storage',
						'ITEMS'             => "16GB|16GB - $399[p+399][c]\r\n32GB|32GB - $499[p+499]\r\n64GB|64GB - $599[p+599]",
						'REQUIRED'          => 'NO',
						'VALIDATIONMESSAGE' => 'Please select the storage size.',
						'FLOW'              => 'VERTICAL',
						'COMPONENTTYPE'     => RSFORM_FIELD_RADIOGROUP
					),
					array(
						'NAME'              => 'Color',
						'CAPTION'           => 'Color',
						'ITEMS'             => "Black|Black[p0]\r\nWhite|White +$39[c][p+39]",
						'REQUIRED'          => 'NO',
						'VALIDATIONMESSAGE' => 'Please select the desired color.',
						'FLOW'              => 'VERTICAL',
						'COMPONENTTYPE'     => RSFORM_FIELD_RADIOGROUP
					),
					array(
						'NAME'              => 'Quantity',
						'CAPTION'           => 'Quantity',
						'ITEMS'             => "1[p1]\r\n2[p2]\r\n3[p3]\r\n4[p4]\r\n5[p5]\r\n6[p6]\r\n7[p7]\r\n8[p8]\r\n9[p9]\r\n10[p10]",
						'REQUIRED'          => 'NO',
						'MULTIPLE'          => 'NO',
						'VALIDATIONMESSAGE' => 'Invalid input.',
						'FLOW'              => 'VERTICAL',
						'COMPONENTTYPE'     => RSFORM_FIELD_SELECTLIST
					),
					array(
						'NAME'              => 'Total Title',
						'TEXT'              => '<h3>Current Total</h3>',
						'COMPONENTTYPE'     => RSFORM_FIELD_FREETEXT
					),
					array(
						'NAME'              => 'Total',
						'CAPTION'           => 'Total',
						'DEFAULTVALUE'      => '0.00',
						'REQUIRED'          => 'NO',
						'SIZE'              => '20',
						'VALIDATIONRULE'    => 'none',
						'VALIDATIONMESSAGE' => 'Invalid input.',
						'ADDITIONALATTRIBUTES' => 'readonly="readonly"',
						'COMPONENTTYPE'     => RSFORM_FIELD_TEXTBOX
					),
					array(
						'NAME'              => 'Customer Info Title',
						'TEXT'              => '<h3>Customer Information</h3>',
						'COMPONENTTYPE'     => RSFORM_FIELD_FREETEXT
					),
					array(
						'NAME'              => 'Email',
						'CAPTION'           => 'Your Email',
						'REQUIRED'          => 'YES',
						'SIZE'              => '20',
						'VALIDATIONRULE'    => 'email',
						'VALIDATIONMESSAGE' => 'Please let us know your email address.',
						'COMPONENTTYPE'     => RSFORM_FIELD_TEXTBOX
					),
					array(
						'NAME'              => 'Send',
						'CAPTION'           => '',
						'LABEL'             => 'Confirm my order',
						'RESET'             => 'NO',
						'BUTTONTYPE'        => 'TYPEBUTTON',
						'COMPONENTTYPE'     => RSFORM_FIELD_SUBMITBUTTON
					)
				);

				$row->Thankyou .=
					'<p>Here\'s your order summary:</p>
<table class="table">
	<thead>
		<tr>
			<th>Product</th>
			<th>Quantity</th>
			<th>Unit Price</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Phone {Storage:value}</td>
			<td>{Quantity:value}</td>
			<td>${Storage:price}</td>
		</tr>
		<tr>
			<td>Color: <em>{Color:value}</em></td>
			<td>&nbsp;</td>
			<td>${Color:price}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
			<td>${Total:value}</td>
		</tr>
		</tbody>
</table>';

				$calculation = (object) array(
					'formId'        => $formId,
					'total'         => 'Total',
					'expression'    => '( {Storage:value} + {Color:value} ) * {Quantity:value}',
					'ordering'      => 1
				);

				$db->insertObject('#__rsform_calculations', $calculation);
				break;
		}

		if (empty($fields))
		{
			return false;
		}

		foreach ($fields as $order => $properties)
		{
			$component = (object) array(
				'FormId'            => $formId,
				'ComponentTypeId'   => $properties['COMPONENTTYPE'],
				'Order'             => $order,
				'Published'         => 1
			);

			$db->insertObject('#__rsform_components', $component, 'ComponentId');

			// No longer needed
			unset($properties['COMPONENTTYPE']);

			foreach ($properties as $key => $value)
			{
				$property = (object) array(
					'ComponentId'   => $component->ComponentId,
					'PropertyName'  => $key,
					'PropertyValue' => $value
				);

				$db->insertObject('#__rsform_properties', $property);
			}

			if (!empty($data['AdminEmail']))
			{
				$row->AdminEmailText .= "\n" . '<p>{' . $properties['NAME'] . ':caption}: {' . $properties['NAME'] . ':value}</p>';
			}

			if (!empty($data['UserEmail']))
			{
				$row->UserEmailText .= "\n" . '<p>{' . $properties['NAME'] . ':caption}: {' . $properties['NAME'] . ':value}</p>';
			}
		}

		if (!empty($data['UserEmail']))
		{
			$row->UserEmailTo = '{Email:value}';
		}

		return true;
	}
}