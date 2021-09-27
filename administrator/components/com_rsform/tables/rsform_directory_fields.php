<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Directory_Fields extends JTable
{		
	public $formId;
	public $componentId;
	public $viewable = 0;
	public $searchable = 0;
	public $editable = 0;
	public $indetails = 0;
	public $incsv = 0;
	public $ordering;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	 
	public function __construct(& $db) {
		parent::__construct('#__rsform_directory_fields', array('formId', 'componentId'), $db);
	}
}