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

    $unique = rand(1000, 9000);
?>

<div class="guru-module-search">
    <form id="guru-search-form" name="guruSearchForm<?php echo $unique; ?>" action="index.php" method="post" class="uk-search uk-search-default">
        <a href="#" onclick="document.guruSearchForm<?php echo $unique; ?>.submit();" class="uk-search-icon-flip uk-search-icon uk-icon" uk-search-icon=""><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg></a>
        <input class="uk-search-input" type="search" placeholder="<?php echo JText::_("GURU_SEARCH_PLACEHOLDER"); ?>" name="search" value="<?php echo $search; ?>" />

        <input type="hidden" name="controller" value="guruSearch" />
        <input type="hidden" name="view" value="guruSearch" />
        <input type="hidden" name="task" value="search" />
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="module_title" value="<?php echo trim($module->title); ?>" />
    </form>
</div>
