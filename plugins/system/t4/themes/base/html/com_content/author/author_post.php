<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
//use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use T4\Helper\J3J4;

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);
$params->set('show_author',0);
// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

?>

<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
<div class="item-content">

	<?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>

	<?php if($params->get('show_date',1) || $params->get('show_hits')): ?>
		<div class="article-aside">
		<dl class="article-info text-muted">
		<?php if($params->get('show_category')) :?>
			<dd class="category">
				<?php echo LayoutHelper::render('joomla.content.info_block.category', array('item' => $this->item, 'params' => $params)); ?>
			</dd>
		<?php endif;?>

		<?php if($params->get('show_date',1)): ?>		
		<?php
			$dateField = $params->get('show_date_field','created');
			$dateFormat = $params->get('show_date_format',JText::_('DATE_FORMAT_LC3'));
			switch ($dateField) {
				case 'modified':
					$T4date = JText::sprintf('COM_CONTENT_LAST_UPDATED', HTMLHelper::_('date', $this->item->{$dateField}, $dateFormat));
					$itemPropDate = "dateModified";
					break;
				case 'publish_up':
					$T4date = JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', HTMLHelper::_('date', $this->item->{$dateField}, $dateFormat));
					$itemPropDate = "datePublished";
					break;
					
				default:
					$T4date = JText::sprintf('COM_CONTENT_CREATED_DATE_ON', HTMLHelper::_('date', $this->item->{$dateField}, $dateFormat));
					$itemPropDate = "dateCreated";
					break;
			}
		?>
		<dd class="create">
			<span class="fa fa-calendar" aria-hidden="true"></span>
			<time datetime="<?php echo HTMLHelper::_('date', $this->item->{$dateField}, 'c'); ?>" itemprop="<?php echo $itemPropDate;?>">
				<?php echo $T4date; ?>
			</time>
		</dd>
		<?php endif ?>
		<?php if($params->get('show_hits')): ?>
		<dd class="hits">
			<span class="fa fa-eye" aria-hidden="true"></span>
			<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>">
			<?php echo Text::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
		</dd>
		<?php endif ?>
	</dl>
	</div>
	<?php endif; ?>

	<div class="intro-txt">
		<?php echo JHtml::_('string.truncate',$this->item->introtext,200,false,false); ?>
	</div>	
</div>

