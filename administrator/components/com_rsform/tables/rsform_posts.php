<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Posts extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $form_id 	= null;
	public $enabled 	= 0;
	public $method	 	= 1;
	public $fields		= null;
	public $headers		= null;
	public $silent	 	= 1;
	public $url	 		= 'https://';

	protected $_jsonEncode = array('fields', 'headers');
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_posts', 'form_id', $db);
	}

	public function hasPrimaryKey()
	{
		$db 	= $this->getDbo();
		$key 	= $this->getKeyName();
		$table	= $this->getTableName();

		$query = $db->getQuery(true)
			->select($db->qn($key))
			->from($db->qn($table))
			->where($db->qn($key) . ' = ' . $db->q($this->{$key}));

		return $db->setQuery($query)->loadResult() !== null;
	}

	public function load($keys = null, $reset = true)
	{
		$result = parent::load($keys, $reset);

		if (!empty($this->fields))
		{
			$this->fields = json_decode($this->fields);

			if (!is_array($this->fields))
			{
				$this->fields = array();
			}
		}

		if (!empty($this->headers))
		{
			$this->headers = json_decode($this->headers);

			if (!is_array($this->headers))
			{
				$this->headers = array();
			}
		}

		return $result;
	}
}