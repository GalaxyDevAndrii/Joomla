<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

?>
<div class="com-content-category category-list <?php echo $this->pageclass_sfx;?>">

<?php
$this->subtemplatename = 'articles';
echo LayoutHelper::render('joomla.content.category_default', $this);
?>

</div>
