<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRsform_Emails extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	
	public $id;
	public $formId;
	public $from = '';
	public $fromname = '';
	public $replyto = '';
	public $replytoname = '';
	public $to = '';
	public $cc = '';
	public $bcc = '';
	public $subject = '';
	public $mode = 1;
	public $message = '';
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_emails', 'id', $db);
	}
}