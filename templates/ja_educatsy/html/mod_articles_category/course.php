<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$textAlign 			= $params->get('sub-align');
?>
<div class="category-grid-view view-courses article-list<?php echo $moduleclass_sfx; ?>" >
	<?php if ($params->get('desc-category')) : ?>
		<div class="desc-category text-<?php echo $textAlign ;?>">
			<?php echo $params->get('desc-category');?>
		</div>
	<?php endif ;?>
	
	<div class="row">
		<?php if ($grouped) : ?>
			<?php foreach ($list as $group_name => $group) : ?>
				<?php foreach ($group as $item) : ?>
					<div class="col-12 col-sm-12 col-md-6 col-xl-4 col">
					<div class="item-inner">
						<?php
							// Intro Image
							$introImage = json_decode($item->images)->image_intro;

							// Get Extrafields
							$extrafields = new JRegistry($item->attribs);

							$coursePrice = $extrafields->get('price');
							$coursePrOff = $extrafields->get('price-off');
							$courseStudents = $extrafields->get('students');
							$courseTime = $extrafields->get('time');
						?>

						<div class="article-content">
							<!-- Intro Image -->
							<div class="intro-image">
								<?php if($introImage) : ?>
									<img src="<?php echo $introImage ;?>" alt="Intro Image" />
								<?php else : ?>
									<img src="images/joomlart/default.jpg" alt="No Image" />
								<?php endif ;?>
							</div>

							<?php if($item->displayCategoryTitle || $item->displayDate) :?>
							<div class="article-top-meta">
								<?php if ($item->displayCategoryTitle) : ?>
									<div class="category">
										<?php echo $item->displayCategoryTitle; ?>
									</div>
								<?php endif; ?>

								<?php if ($item->displayDate) : ?>
									<div class="articles-date">
										<?php echo $item->displayDate; ?>
									</div>
								<?php endif; ?>

								<?php if ($params->get('show_author')) : ?>
									<div class="articles-writtenby">
										<?php echo '<span>'.$item->displayAuthorName.'</span>'; ?>
									</div>
								<?php endif; ?>

								<?php if ($item->displayHits) : ?>
									<div class="articles-hits">
										<i class="fa fa-eye" aria-hidden="true"></i> <?php echo $item->displayHits; ?>
									</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>

							<!-- Title -->
							<?php if ($params->get('link_titles') == 1) : ?>
								<div class="title">
									<h4>
										<a class="mod-articles-category-title heading-link <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
											<?php echo $item->title; ?>
										</a>
									</h4>
								</div>
							<?php else : ?>

								<h4>
									<?php echo $item->title; ?>
								</h4>
							<?php endif; ?>

							<?php if ($params->get('show_introtext')) : ?>
								<div class="articles-introtext">
									<?php echo $item->displayIntrotext; ?>
								</div>
							<?php endif; ?>

							<?php if ($params->get('show_tags', 0) && $item->tags->itemTags) : ?>
								<div class="mod-articles-category-tags">
									<?php echo JLayoutHelper::render('joomla.content.tags', $item->tags->itemTags); ?>
								</div>
							<?php endif; ?>

							
						</div>

						<div class="article-bottom">
							<?php if($courseStudents || $courseTime) :?>
							<div class="course-info">
								<?php if($courseStudents) :?>
									<div class="users d-flex">
										<span class="fas fa-user"></span>
										<?php echo $courseStudents.' '.Jtext::_('TPL_MOD_STUDENTS') ;?>
									</div>
								<?php endif ;?>

								<?php if($courseTime) :?>
									<div class="durations d-flex">
										<span class="far fa-clock"></span>
										<?php echo $courseTime ;?>
									</div>
								<?php endif ;?>
							</div>
							<?php endif ;?>

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

								<?php if ($params->get('show_readmore')) : ?>
									<a class="articles-title btn btn-default <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
										<?php if ($item->params->get('access-view') == false) : ?>
											<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
										<?php else : ?>
											<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
										<?php endif; ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php foreach ($list as $item) : ?>
				<div class="col-12 col-sm-12 col-md-6 col-xl-4 col">
					<div class="item-inner">
						<?php
							// Intro Image
							$introImage = json_decode($item->images)->image_intro;

							// Get Extrafields
							$extrafields = new JRegistry($item->attribs);

							$coursePrice = $extrafields->get('price');
							$coursePrOff = $extrafields->get('price-off');
							$courseStudents = $extrafields->get('students');
							$courseTime = $extrafields->get('time');
						?>

						<div class="article-content">
							<!-- Intro Image -->
							<div class="intro-image">
								<?php if($introImage) : ?>
									<img src="<?php echo $introImage ;?>" alt="Intro Image" />
								<?php else : ?>
									<img src="images/joomlart/default.jpg" alt="No Image" />
								<?php endif ;?>
							</div>

							<?php if($item->displayCategoryTitle || $item->displayDate) :?>
							<div class="article-top-meta">
								<?php if ($item->displayCategoryTitle) : ?>
									<div class="category">
										<?php echo $item->displayCategoryTitle; ?>
									</div>
								<?php endif; ?>

								<?php if ($item->displayDate) : ?>
									<div class="articles-date">
										<?php echo $item->displayDate; ?>
									</div>
								<?php endif; ?>

								<?php if ($params->get('show_author')) : ?>
									<div class="articles-writtenby">
										<?php echo '<span>'.$item->displayAuthorName.'</span>'; ?>
									</div>
								<?php endif; ?>

								<?php if ($item->displayHits) : ?>
									<div class="articles-hits">
										<i class="fa fa-eye" aria-hidden="true"></i> <?php echo $item->displayHits; ?>
									</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>

							<!-- Title -->
							<?php if ($params->get('link_titles') == 1) : ?>
								<div class="title">
									<h4>
										<a class="mod-articles-category-title heading-link <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
											<?php echo $item->title; ?>
										</a>
									</h4>
								</div>
							<?php else : ?>

								<h4>
									<?php echo $item->title; ?>
								</h4>
							<?php endif; ?>

							<?php if ($params->get('show_introtext')) : ?>
								<div class="articles-introtext">
									<?php echo $item->displayIntrotext; ?>
								</div>
							<?php endif; ?>

							<?php if ($params->get('show_tags', 0) && $item->tags->itemTags) : ?>
								<div class="mod-articles-category-tags">
									<?php echo JLayoutHelper::render('joomla.content.tags', $item->tags->itemTags); ?>
								</div>
							<?php endif; ?>

							
						</div>

						<div class="article-bottom">
							<?php if($courseStudents || $courseTime) :?>
							<div class="course-info">
								<?php if($courseStudents) :?>
									<div class="users d-flex">
										<span class="fas fa-user"></span>
										<?php echo $courseStudents.' '.Jtext::_('TPL_MOD_STUDENTS') ;?>
									</div>
								<?php endif ;?>

								<?php if($courseTime) :?>
									<div class="durations d-flex">
										<span class="far fa-clock"></span>
										<?php echo $courseTime ;?>
									</div>
								<?php endif ;?>
							</div>
							<?php endif ;?>

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

								<?php if ($params->get('show_readmore')) : ?>
									<a class="articles-title btn btn-default <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
										<?php if ($item->params->get('access-view') == false) : ?>
											<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
										<?php else : ?>
											<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
										<?php endif; ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>


