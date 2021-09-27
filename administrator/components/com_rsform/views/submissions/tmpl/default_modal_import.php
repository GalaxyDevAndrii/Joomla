<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

JText::script('COM_RSFORM_PLEASE_UPLOAD_ONLY_CSV_FILES');
JText::script('COM_RSFORM_PLEASE_UPLOAD_A_FILE');
?>
<div class="container-fluid">
	<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
		<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
			<div class="alert alert-error" id="importError" style="display: none;"></div>
			<fieldset class="form-horizontal">
				<?php
				if ($fields = $this->filterForm->getGroup('import'))
				{
					foreach ($fields as $field)
					{
						echo $field->renderField();
					}
				}
				?>
            </fieldset>
		</div>
	</div>
</div>