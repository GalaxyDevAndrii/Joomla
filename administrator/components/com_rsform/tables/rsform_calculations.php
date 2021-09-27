<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRsform_Calculations extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	
	public $id;
	public $formId;
	public $total = '';
	public $expression = '';
	public $ordering = '';
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_calculations', 'id', $db);
	}

	public function check()
	{
		if (!$this->ordering)
		{
			$db = $this->getDbo();
			$this->ordering = $this->getNextOrder($db->qn('formId') . ' = ' . $db->q($this->formId));
		}

		return true;
	}
}