<?php 
use Joomla\CMS\Language\Text;
$params = $displayData['params'];
$pagination = $displayData['pagination'];
$elm = $displayData['elem'];
$isowrap = !empty($displayData['isowrap']) ? $displayData['isowrap'] : "";
$show_pagination = $params->get('show_pagination', 1);

if ($pagination->pagesTotal <= 1) return;
// build next page link for load more or load infinitive
if ($show_pagination == 3 || $show_pagination == 4) {
	$data = $pagination->getData();
	$pages = $data->pages;
}
?>

<?php if ($show_pagination == 1 || $show_pagination == 2) : ?>
	<div class="com-content-category-blog__navigation w-100">
		<div class="com-content-category-blog__pagination">
			<?php echo $pagination->getPagesLinks(); ?>
		</div>

		<?php if ($params->def('show_pagination_results', 1)) : ?>
			<p class="com-content-category-blog__counter counter">
				<?php echo $pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>
	</div>
<?php elseif ($show_pagination == 3 || $show_pagination == 4): ?> 

	<div class="com-content-category-blog__pagination_all">
		<?php foreach ($pages as $page) : ?>
			<a href="<?php echo $page->link ?>" aria-label="<?php echo Text::sprintf('JLIB_HTML_GOTO_PAGE', strtolower($page->text)) ?>"><?php echo $page->text ?></a>
		<?php endforeach ?>
	</div>

	<div class="text-center mt-3">
		<span class="btn btn-secondary btn-load-more"><?php echo Text::_('T4_LOAD_MORE'); ?></span>
	</div>

	<div class="alert alert-info ending-msg text-center load-more-end-msg mt-3" style="display: none">
		<?php echo Text::_('T4_LOADMORE_END_MSG'); ?>
	</div>

	<script type="text/javascript">
		(function($) {
			var pagesCurrent = <?php echo $pagination->pagesCurrent ?>;
			var pagesTotal = <?php echo $pagination->pagesTotal ?>;
			var $load_more_btn = $('.btn-load-more');
			var loading = false;
			var load_more = function() {
				if (loading) return;
				// get current page
				var $pages = $('.com-content-category-blog__pagination_all > a');
				if ($pages.length <= pagesCurrent) {
					// call done
					load_end();
					return;
				}
				var $page = $pages.eq(pagesCurrent);
				loading = true;
				$('body').addClass('loading-more');
				$.ajax($page.attr('href') + '&loadpage').done(function(data) {
					if($('.layout-isotope').length){
						var items = $(data).children();
						// HTML response
						$container = $('<?php echo $isowrap;?>');
						$container.append(items).isotope('appended', items);
						$container.imagesLoaded().progress(function(){
							$container.isotope('layout');
						});
					}else{
						$(data).children().appendTo('<?php echo $elm;?>');
					}
					if (++pagesCurrent >= $pages.length) {
						// call done
						load_end();
					}
					loading = false;
					$('body').removeClass('loading-more');
				})
			}

			var load_end = function() {
				// remove load more button
				$load_more_btn.remove();
				// show nomore content
				$('.load-more-end-msg').show();
			}

			var btn_bind = function() {
				$load_more_btn.on('click', load_more);
			}

			var scroll_bind = function() {
				$load_more_btn.css({opacity:0});
				var $win = $(window);
				$(window).on('scroll', function() {
					if ($win.scrollTop() + $win.height() >= $load_more_btn.offset().top) {
						load_more();
					}
				})
			}

		<?php if ($pagination->pagesTotal <= $pagination->pagesCurrent): ?>
			load_end();
		<?php elseif ($show_pagination == 3) :
			// load more style	
			?>	
			btn_bind ();
		<?php elseif ($show_pagination == 4) : 
			// infinitive style
			?>
			scroll_bind ();			
		<?php endif ?>

			// hide pagination link
			$('.com-content-category-blog__pagination_all').hide();
		})(jQuery);
	</script>

<?php endif; ?>
