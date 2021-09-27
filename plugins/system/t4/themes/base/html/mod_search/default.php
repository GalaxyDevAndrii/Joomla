<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

?>
<div class="mod-search search <?php echo $moduleclass_sfx; ?>">
	<form action="<?php echo Route::_('index.php'); ?>" method="post">
		<?php
			$input  = '<input name="searchword" id="mod-search-searchword' . $module->id . '" class="form-control" type="search" placeholder="' . $text . '">';
			$output = '';

			if ($button) :
				if ($imagebutton) :
					$btn_output = '<input type="image" alt="' . $button_text . '" class="btn btn-primary" src="' . $img . '" onclick="this.form.searchword.focus();">';
				else :
					$btn_output = '<button class="btn btn-primary" onclick="this.form.searchword.focus();">' . $button_text . '</button>';
				endif;

				$output .= '<div class="input-group">';
				$output .= $input;
				$output .= '<span class="input-group-append">';
				$output .= $btn_output;
				$output .= '</span>';
				$output .= '</div>';
			else :
				$output .= $input;
			endif;

			echo $output;
		?>
		<input type="hidden" name="option" value="com_search">
		<input type="hidden" name="task" value="search">
		<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>">
	</form>
</div>
