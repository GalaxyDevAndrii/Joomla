<?php 
    // no direct access
    defined( '_JEXEC' ) or die( 'Restricted access' );
    defined('DS') or define("DS", DIRECTORY_SEPARATOR);
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'modules/mod_guru_search/mod_guru_search.css' );
    
    echo '
        <script type="text/javascript">
            guru_site_host = "'.JURI::root().'";
        </script>
        <script type="text/javascript" src="'.JURI::root().'components/com_guru/js/ukconflict.js"></script>
        <script type="text/javascript" src="'.JURI::root().'components/com_guru/js/uikit.min.js"></script>
    ';

    $search = JFactory::getApplication()->input->get("search", "", "raw");
?>

<div class="guru-module-search">
    <form id="guru-search-form" name="guru-search-form-<?php echo $module->id ?>" action="<?php echo JRoute::_("index.php?option=com_guru&view=gurusearch") ?>" method="get" class="uk-search uk-search-default">
        <button type="submit" class="btn-primary btn uk-search-button" >
            <?php echo Jtext::_('TPL_SEARCH_NOW') ;?>
        </button>
        <input class="uk-search-input" type="search" placeholder="<?php echo JText::_("TPL_GURU_SEARCH_PLACEHOLDER"); ?>" name="search" value="<?php echo $search; ?>" />

        <input type="hidden" name="module_id" value="<?php echo $module->id ?>" />
    </form>
</div>
