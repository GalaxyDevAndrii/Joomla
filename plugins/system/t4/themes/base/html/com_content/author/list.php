<?php
use Joomla\CMS\User\UserHelper;
$app = JFactory::getApplication();
// process for ajax pagination, just get items content and return to browser
$isAjax = $app->input->get('loadpage') !== null;
if ($isAjax) {
	echo $this->loadTemplate('items');
	exit;
}

?>
<div class="author-page">
	<div class="container">
			<?php echo $this->loadTemplate('items'); ?>
	</div>
</div>
<?php echo JLayoutHelper::render('t4.content.pagination', ["params"=> $this->params,'pagination'=> $this->AuhtorPagination,'elem'=>'.author-page .author-lists'] , T4PATH_BASE . '/html/layouts'); ?>