<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace T4\MVC\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
/**
 * HTML View class for the Content component
 *
 * @since  1.5
 */

class Author extends ListModel
{
	protected $list_limit = null;

	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $_item = null;
	protected $task = "author";

	
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_content.author';
	/**
	 * Array of articles in the author
	 *
	 * @var    \stdClass[]
	 */
	protected $_articles = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'modified', 'a.modified',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'author', 'a.author',
				'filter_tag'
			);
		}

		parent::__construct($config);
		$this->list_limit = Factory::getConfig()->get('list_limit');
	}
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		// Get the current user for authorisation checks
		$app = Factory::getApplication();
		$params = $app->getParams();
		$input = Factory::getApplication()->input;
		$gid = $input->getint('gid','');
		$user_id = $input->getInt('id','');
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$contentParams = \JComponentHelper::getParams('com_content');
		// $mergedParams->merge($params,$contentParams);
		parent::populateState($ordering = 'ordering', $direction = 'ASC');

		if($user_id){

			$articleLimit = $mergedParams->get('num_intro_articles',$this->list_limit);
			$this->setState('params', $mergedParams);
			$this->setState('filter.author_id',$user_id);
			$this->setState('filter.author_id.include','=');
			$this->setState('list.limit',$articleLimit);
			$this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
			$userParams = $this->getUserParams($user_id);
			$this->setState('profile',$userParams->get('profile'));
			$this->setState('fields',$userParams->get('fields'));
			
		}else{
			$this->setState('gid',$gid);
			$limitAuthors = $mergedParams->get('num_authors',$this->list_limit);
			$this->setState('params', $mergedParams);
			$this->setState('author.limit',$limitAuthors);
			$this->setState('author.start', $app->input->get('limitstart', 0, 'uint'));
		}
	}
		/**
	 * Get the articles in the category
	 *
	 * @return  mixed  An array of articles or false if an error occurs.
	 *
	 * @since   1.5
	 */
	public function getItems()
	{
		$limit = $this->getState('list.limit');

		if ($this->_articles === null)
		{
			if(\T4\Helper\J3J4::isJ3()){
				$model = \JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
			}else{

				$model = $this->bootComponent('com_content')->getMVCFactory()
					->createModel('Articles', 'Site', ['ignore_request' => true]);
			}

			$model->setState('params', Factory::getApplication()->getParams());
			$model->setState('filter.published', $this->getState('filter.published'));
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('filter.language', $this->getState('filter.language'));
			$model->setState('filter.featured', $this->getState('filter.featured'));
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.limit', $limit);
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.filter', $this->getState('list.filter'));
			$model->setState('filter.tag', $this->getState('filter.tag'));
			$model->setState('filter.author_id', $this->getState('filter.author_id'));
			$model->setState('filter.author_id.include', $this->getState('filter.author_id.include'));
			if ($limit >= 0)
			{
				$this->_articles = $model->getItems();
		
				if ($this->_articles === false)
				{
					$this->setError($model->getError());
				}
			}
			else
			{
				$this->_articles = array();
			}

			$this->_pagination = $model->getPagination();
		}

		return $this->_articles;
	}
	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  \JPagination  A JPagination object for the data set.
	 *
	 * @since   3.0.1
	 */
	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			return null;
		}

		return $this->_pagination;
	}
	public function getAuthors()
	{
		// Get a storage key.
		$store = $this->getStoreId('authors');
		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		try
		{
			// Load the list items and add the items to the internal cache.
			$this->cache[$store] = $this->_getList($this->getAuthorListQuery(), $this->getAuthorStart(), $this->getState('author.limit'));
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
		if(!empty($this->cache[$store])){
			foreach ($this->cache[$store] as $author) {
				$userParams = $this->getUserParams($author->id);
				$author->profile = $userParams->get('profile');
				$author->fields = $userParams->get('fields');
				$author->link = Route::_('index.php?option=com_content&view=author&layout=author&id='.$author->id,false);
			}
		}
		return $this->cache[$store];
	}
	
	public function getAuthor($userid = null){
		if($userid) $this->setState('filter.author_id',$userid);
		$this->getDbo()->setQuery($this->getAuthorListQuery());
		$user = $this->getDbo()->loadObject();
		if(!$userid) $userid = $this->getState('filter.author_id');
		$userParams = $this->getUserParams($userid);
		$user->profile = $userParams->get('profile');
		$user->fields = $userParams->get('fields');
		$user->link = Route::_('index.php?option=com_content&view=author&layout=author&id='.$userid,false);
		return $user;
	}

	protected function getAuthorListQuery()
	{

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select('u.id, u.name, u.username, u.params');

		$query->from('#__users AS u');
		$query->join('left','#__user_usergroup_map AS g ON g.user_id = u.id');
		if($this->getState('filter.author_id','')){
			$query->where('u.id = '. $db->quote($this->getState('filter.author_id','')));
		}else{
			$query->where('g.group_id IN ('. implode(",",$this->getState('gid')).")");
		}
		$query->group('u.id');

		return $query;
	}
	/**
	 * Method to get a \JPagination object for the data set.
	 *
	 * @return  \JPagination  A \JPagination object for the data set.
	 *
	 * @since   1.6
	 */
	public function getAuthorPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getAuthorPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$limit = (int) $this->getState('author.limit') - (int) $this->getState('author.links');
		// Create the pagination object and add the object to the internal cache.
		$this->cache[$store] = new \JPagination($this->getAuthorTotal(), $this->getAuthorStart(), $limit);

		return $this->cache[$store];
	}
	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since   1.6
	 */
	public function getAuthorTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getAuthorTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		try
		{
			// Load the total and add the total to the internal cache.
			$this->cache[$store] = (int) $this->_getListCount($this->getAuthorListQuery());
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 *
	 * @since   1.6
	 */
	public function getAuthorStart()
	{
		$store = $this->getStoreId('getAuthorstart');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		$app = Factory::getApplication();
		// $start = $app->input->get('limitstart', 0, 'uint');
		$start = $this->getState('author.start',0);
		if ($start > 0)
		{
			$limit = $this->getState('author.limit');
			$total = $this->getAuthorTotal();

			if ($start > $total - $limit)
			{
				$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
			}
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}
	public function getUserParams($userId = null){

		if(!$userId) return "";
		$data = array();

		$params = new Registry;
		if($this->requireUserProfile()){
			//get profile params
			// Load the profile data from the database.
			$db = Factory::getDbo();
			$db->setQuery(
				'SELECT profile_key, profile_value FROM #__user_profiles'
					. ' WHERE user_id = ' . (int) $userId . " AND profile_key LIKE 'profile.%'"
					. ' ORDER BY ordering'
			);

			try
			{
				$profiles = $db->loadRowList();
			}
			catch (RuntimeException $e)
			{
				$this->_subject->setError($e->getMessage());

				return false;
			}
			if(!empty($profiles)){
				// Merge the profile data.
				$data = array();

				foreach ($profiles as $v)
				{
					$k = str_replace('profile.', '', $v[0]);
					$data[$k] = json_decode($v[1], true);

					if ($data[$k] === null)
					{
						$data[$k] = $v[1];
					}
				}
			}
		}

		$params->set('profile', new Registry($data));
		//get field params
		$user = Factory::getUser($userId);
		if(\T4\Helper\J3J4::isJ3()){
			\JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
			$fields = \FieldsHelper::getFields('com_users.user',$user);
		}else{
			$fields = Joomla\Component\Fields\Administrator\Helper\FieldsHelper::getFields('com_users.user',$user);
		}
		$userField = array();
		if(!empty($fields)){
			foreach ($fields as $field) {
				$userField[$field->name] = $field->value;
			}
		}
		$params->set('fields', new Registry($userField));
		return $params;
	}
	public function requireUserProfile(){
		//check required plugin user profile
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('enabled')
		->from('#__extensions')
		->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
		->where($db->quoteName('element') . ' = ' . $db->quote('profile'));
		return $db->setQuery($query)->loadResult();
	}
}