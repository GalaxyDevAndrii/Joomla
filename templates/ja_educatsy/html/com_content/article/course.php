<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
//use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use T4\Helper\J3J4;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = Factory::getUser();
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

// Get Extrafields
$extrafields = new JRegistry($this->item->attribs);

$coursePrice = $extrafields->get('price');
$coursePrOff = $extrafields->get('price-off');
$courseStudents = $extrafields->get('students');
$courseTime = $extrafields->get('time');
$courseLevel = $extrafields->get('level');
$courseLink = $extrafields->get('link-course');

$courseList = $extrafields->get('course-list');
$courseTitle = $extrafields->get('course-title');

$courseRequ = $extrafields->get('course-requirements');


?>
<div class="com-content-article view-course-details item-page<?php echo $this->pageclass_sfx; ?>">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getApplication()->get('language') : $this->item->language; ?>">

	<div class="row">
		<div class="order-lg-2 col-lg-4">
			<div class="course-sidebar">
				<?php if ($params->get('access-view')) : ?>
					<?php echo LayoutHelper::render('joomla.content.full_image', $this->item); ?>
				<?php endif; ?>

				<div class="course-info">
					<?php // Todo Not that elegant would be nice to group the params ?>
						<?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
						|| $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam); ?>

						<?php if (!$useDefList && $this->print) : ?>
							<div id="pop-print" class="btn hidden-print clearfix">
								<?php echo HTMLHelper::_('icon.print_screen', $this->item, $params); ?>
							</div>
						<?php endif; ?>

						<?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
							<?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
						<?php endif; ?>

						<?php if (!$this->print) : ?>
							<?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
								<?php echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
							<?php endif; ?>
						<?php else : ?>
							<?php if ($useDefList) : ?>
								<div id="pop-print" class="btn hidden-print">
									<?php echo HTMLHelper::_('icon.print_screen', $this->item, $params); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>

					<?php if ($params->get('show_title') || $params->get('show_author')) : ?>
						<div class="page-header">
							<?php if ($params->get('show_title')) : ?>
								<h2 itemprop="headline">
									<?php echo $this->escape($this->item->title); ?>
								</h2>
							<?php endif; ?>

							<?php if (J3J4::checkUnpublishedContent($this->item)) : ?>
								<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
							<?php endif; ?>

							<?php if (strtotime($this->item->publish_up) > strtotime(Factory::getDate())) : ?>
								<span class="badge badge-warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
							<?php endif; ?>

							<?php if ((strtotime($this->item->publish_down) < strtotime(Factory::getDate())) && $this->item->publish_down != Factory::getDbo()->getNullDate()) : ?>
								<span class="badge badge-warning"><?php echo Text::_('JEXPIRED'); ?></span>
							<?php endif; ?>

						</div>
					<?php endif; ?>

					<ul>
						<?php if($courseStudents) :?>
							<li>
								<span class="label">
									<span class="fas fa-graduation-cap text-primary"></span>
									<?php echo Jtext::_('TPL_NUM_STUDENTS') ;?>:
								</span>

								<span class="content">
									<?php echo $courseStudents ;?>
								</span>
							</li>
						<?php endif ;?>

						<?php if($courseTime) :?>
							<li>
								<span class="label">
									<span class="far fa-clock text-primary"></span>
									<?php echo Jtext::_('TPL_DURATIONS') ;?>:
								</span>

								<span class="content">
									<?php echo $courseTime ;?>
								</span>
							</li>
						<?php endif ;?>

						<?php if($courseLevel) :?>
							<li>
								<span class="label">
									<span class="fas fa-signal text-primary"></span>
									<?php echo Jtext::_('TPL_SIGNAL') ;?>:
								</span>

								<span class="content">
									<?php echo $courseLevel ;?>
								</span>
							</li>
						<?php endif ;?>
					</ul>

					<div class="course-action">
						<?php if($coursePrice || $coursePrOff) :?>
							<div class="extra-price">
								<?php if($coursePrice) :?>
									<div class="price-base <?php if($coursePrOff) echo 'has-price-off'?>"><?php echo $coursePrice ;?></div>
								<?php endif ;?>

								<?php if($coursePrOff) :?>
									<div class="price-off"><?php echo $coursePrOff ;?></div>
								<?php endif ;?>
							</div>
						<?php endif ;?>

						<?php if($courseLink) :?>
						<div class="course-btn">
							<a href="<?php echo $courseLink ;?>" title="<?php echo Jtext::_('TPL_TAKE_COURSE') ;?>" class="btn btn-primary btn-block">
								<?php echo Jtext::_('TPL_TAKE_COURSE') ;?>
							</a>
						</div>
						<?php endif ;?>
					</div>
				</div>
			</div>
		</div>

		<div class="order-lg-1 col-lg-8">
			<ul class="nav nav-tabs" id="courseTab" role="tablist">
			  <li class="nav-item">
			    <a class="nav-link active" id="descriptions-tab" data-toggle="tab" href="#descriptions" role="tab" aria-controls="descriptions" aria-selected="true">
			    	<span class="fas fa-list"></span>
			    	<?php echo Jtext::_('TPL_DESCRIPTIONS') ?>
			    </a>
			  </li>

			  <?php if(!empty($courseList)) :?>
			  <li class="nav-item">
			    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">
			    	<span class="far fa-list-alt"></span>
			    	<?php echo Jtext::_('TPL_CONTENT') ?>
			    </a>
			  </li>
				<?php endif ;?>

			  <?php if(!empty($courseRequ)) :?>
			  <li class="nav-item">
			    <a class="nav-link" id="requirements-tab" data-toggle="tab" href="#requirements" role="tab" aria-controls="requirements" aria-selected="false">
			    	<span class="fas fa-tasks"></span>
			    	<?php echo Jtext::_('TPL_REQUIREMENTS') ?>
			    </a>
			  </li>
			  <?php endif ;?>
			</ul>
			<div class="tab-content" id="courseTabContent">
			  <div class="tab-pane fade show active" id="descriptions" role="tabpanel" aria-labelledby="descriptions-tab">
			  	<?php if ($this->params->get('show_page_heading')) : ?>
						<div class="page-header">
							<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
						</div>
						<?php endif; ?>

						<?php // Content is generated by content plugin event "onContentAfterTitle" ?>
						<?php echo $this->item->event->afterDisplayTitle; ?>

						<?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
							<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
							<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
						<?php endif; ?>

						<?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
						<?php echo $this->item->event->beforeDisplayContent; ?>

						<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
							|| (empty($urls->urls_position) && (!$params->get('urls_position')))) : ?>
							<?php echo $this->loadTemplate('links'); ?>
						<?php endif; ?>

						<?php if ($params->get('access-view')) : ?>
							<?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) :
								echo $this->item->pagination;
							endif; ?>

						<?php if (isset ($this->item->toc)) : echo $this->item->toc; endif; ?>

						<div itemprop="articleBody" class="com-content-article__body">
							<?php echo $this->item->text; ?>
						</div>

						<?php if ($info == 1 || $info == 2) : ?>
							<?php if ($useDefList) : ?>
								<?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
							<?php endif; ?>

							<?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
								<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
								<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
							<?php endif; ?>
						<?php endif; ?>

						<?php
							if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative) :
								echo $this->item->pagination;
							endif;
						?>

						<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
							<?php echo $this->loadTemplate('links'); ?>
						<?php endif; ?>

						<?php // Optional teaser intro text for guests ?>
						<?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
							<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
							<?php echo HTMLHelper::_('content.prepare', $this->item->introtext); ?>
							<?php // Optional link to let them register to see the whole article. ?>
						
							<?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
							<?php $menu = Factory::getApplication()->getMenu(); ?>
							<?php $active = $menu->getActive(); ?>
							<?php $itemId = $active->id; ?>
							<?php $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
							<?php $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>

							<p class="com-content-article__readmore readmore">
								<a href="<?php echo $link; ?>" class="register">
								<?php $attribs = json_decode($this->item->attribs); ?>
								<?php
								if ($attribs->alternative_readmore == null) :
									echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
								elseif ($readmore = $attribs->alternative_readmore) :
									echo $readmore;
									if ($params->get('show_readmore_title', 0) != 0) :
										echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
									endif;
								elseif ($params->get('show_readmore_title', 0) == 0) :
									echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
								else :
									echo Text::_('COM_CONTENT_READ_MORE');
									echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
								endif; ?>
								</a>
							</p>
							<?php endif; ?>
						<?php endif; ?>

						<?php
							if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) :
								echo $this->item->pagination;
							endif;
						?>

						<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
						<?php echo $this->item->event->afterDisplayContent; ?>
			  </div>
				<?php if(!empty($courseList)) :?>
			  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			  	<!-- COURSE LIST -->
						<?php if($courseTitle):?>
							<h3 class="mb-3 mb-lg-4"><?php echo $courseTitle ;?></h3>
						<?php endif ;?>
						<div id="accordion">
							<?php $i=0; foreach ($courseList as $key=>$value) : $i++; ?>
						  <div class="card">
						    <div class="card-header" id="heading-<?php echo $i ;?>">
					        <a href="javascript:void(0)" class="<?php if($i!=1) echo 'collapsed' ;?> heading-link" data-toggle="collapse" data-target="#collapse-<?php echo $i ;?>" aria-expanded="true" aria-controls="collapse-<?php echo $i ;?>">
					          <?php echo $value->course_title; ?>
					        </a>
						    </div>

						    <div id="collapse-<?php echo $i ;?>" class="collapse <?php if($i==1) echo 'show' ;?>" aria-labelledby="heading-<?php echo $i ;?>" data-parent="#accordion">
						      <div class="card-body">
						       <?php echo $value->course_desc; ?>
						      </div>
						    </div>
						  </div>
						  <?php endforeach ;?>
						</div>
				  <!-- COURSE LIST -->
			  </div>
			  <?php endif ;?>

				<?php if(!empty($courseRequ)) :?>
			  <div class="tab-pane fade" id="requirements" role="tabpanel" aria-labelledby="requirements-tab">
			  	<!-- COURSE LIST -->
					<ul id="requirements-list">
						<?php $i=0; foreach ($courseRequ as $key=>$value) : $i++; ?>
					 		<li><?php echo $value->requirements_title; ?></li>
					  <?php endforeach ;?>
					</ul>
				  <!-- COURSE LIST -->
			  </div>
			  <?php endif ;?>
			</div>
		</div>
	</div>
	
</div>

<?php $introText = preg_replace('/\s\s+/', ' ', strip_tags($this->item->introtext)); ?>
<script>
    (function($){
        $( ".ja-masthead-title" ).html("<?php echo $this->escape($this->item->title); ?>");
        if(!$(".ja-masthead-description").length) {
          $(".ja-masthead-title").after('<div class="ja-masthead-description"></div>');
        }
        $(".ja-masthead-description").html("<?php echo $introText; ?>");
    })(jQuery);
</script>

<script type="application/ld+json">
{
	"@context": "http://schema.org",
	"@type": "Article",
	"headline": "<?php echo $this->item->title; ?>",
	"inLanguage": "<?php echo JFactory::getConfig()->get('language'); ?>",
	"author": "<?php echo $this->item->author; ?>",
	"datePublished": "<?php echo $this->item->publish_up; ?>",
	"dateModified": "<?php echo $this->item->modified; ?>",
	"mainEntityOfPage": "WebPage",
	"articleBody": <?php echo json_encode(preg_replace('/\s+/', ' ', strip_tags($this->item->text))); ?>,
	"image": 
	{
		"@type": "imageObject",
		"url": "<?php echo JUri::base().$images->image_fulltext; ?>",
		"height": "auto",
		"width": "auto"
	},
	"publisher": 
	{
		"@type": "Organization",
		"name": "<?php echo $this->item->author; ?>",
		"logo": 
		{
			"@type": "imageObject",
			"url": "<?php echo JURI::base(); ?>/templates/<?php echo JFactory::getApplication()->getTemplate() ?>/images/logo.png"
		}
	}
}
</script>
