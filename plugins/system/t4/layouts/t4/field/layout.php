<?php 

defined('JPATH_BASE') or die;

extract($displayData);
$doc = \JFactory::getDocument();
//$doc->addScript(JUri::root(true) . '/media/t4/builder/js/loader.js');
$doc->addStylesheet(JUri::root(true) . '/media/t4/builder/css/style.css');

?>
<div class="t4-item-wrapper">

    <input type="hidden" name="<?php echo $name ?>" value="<?php echo htmlentities($value) ?>" data-t4editor />
</div>

