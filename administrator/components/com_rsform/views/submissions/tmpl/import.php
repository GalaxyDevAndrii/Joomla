<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('script', 'com_rsform/admin/import.js', array('relative' => true, 'version' => 'auto'));

JText::script('COM_RSFORM_PLEASE_MAP_AT_LEAST_A_FIELD_FROM_THE_DROPDOWN');
JText::script('COM_RSFORM_YOU_HAVE_SELECTED_MULTIPLE_FIELDS');
JText::script('ERROR');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=submissions&layout=import'); ?>" method="post" name="adminForm" id="adminForm">
    <div style="overflow-x: auto; min-height: 400px;">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <?php
            for ($i = 0; $i < $this->countHeaders; $i++) {
            ?>
                <th>
                    <select name="header[]">
                        <?php echo JHtml::_('select.options', $this->options, 'value', 'text', isset($this->selected[$i]) ? $this->selected[$i] : false); ?>
                    </select>
                </th>
            <?php
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->previewData as $line)
        {
            ?>
            <tr>
                <?php foreach ($line as $column) { ?>
                    <td><?php echo $this->escape($column); ?></td>
                <?php } ?>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    </div>

    <?php echo JHtml::_( 'form.token' ); ?>
    <input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_rsform" />
</form>