
<?php
$introcount = count($this->article_items);
$counter = 0;
$colClass = floor(12/$this->columns);
?>
<?php if (!empty($this->article_items)) : ?>
	<div class="content-item row items-row">
	<?php foreach ($this->article_items as $key => &$item) : ?>
		<?php $rowcount = ((int) $key % (int) $this->columns) + 1; ?>
	<?php if ($rowcount === 1) : ?>
		<?php $row = $counter / $this->columns; ?>
	<?php endif; ?>
		<div class="col-12 col-md-6 col-lg-<?php echo $colClass;?>">
			<div class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
				<?php
				$item->link = JRoute::_(\ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language));
				$this->item = &$item;
				echo $this->loadTemplate('post');
			?>
			</div><!-- end item -->
			<?php $counter++; ?>
		</div><!-- end col -->
	<?php endforeach; ?>
</div>
<?php endif; ?>