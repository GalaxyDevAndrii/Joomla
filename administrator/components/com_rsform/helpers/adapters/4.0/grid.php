<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2009 - 2020 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

abstract class RSFormProAdapterGrid
{
	public static function row()
	{
		return 'row';
	}

	public static function column($size)
	{
		return 'col-md-' . (int) $size;
	}

	public static function sidebar()
	{
		return '<div id="j-main-container" class="j-main-container">';
	}

	public static function inputAppend($input, $append)
	{
		return
		'<div class="input-group">' .
			$input .
			'<div class="input-group-append"><span class="input-group-text">' . $append . '</span></div>' .
		'</div>';
	}
}