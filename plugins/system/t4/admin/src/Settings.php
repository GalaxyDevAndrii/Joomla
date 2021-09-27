<?php

namespace T4Admin;

defined('_JEXEC') or die();

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
use Joomla\CMS\Form\Form as JForm;
use T4Admin\T4form as T4form;

class Settings
{
    private static function getInputElements($key, $field)
    {
        return call_user_func(array( 'T4Field' . ucfirst($key), 'getInput'), $key, $field);
    }
    public static function getRowSettings()
    {
        $tplXml = T4PATH_TPL . '/templateDetails.xml';
        // new form
        $xmlfile = \T4\Helper\Path::findInTheme('params/layout.xml');
        $form = JForm::getInstance('t4layout', $xmlfile);
        // if ($xmlfile) $form->loadFile($xmlfile);
        $form = new T4form($form);
        //remove all fields from group 'params' and reload them again in right other base on template.xml
        $form->removeGroup('t4layout');
        //load the template
        $form->loadFile($xmlfile);
        //overwrite / extend with params of template
        $form->loadFile($tplXml, true, '//layouts');
        $fieldsets = $form->getFieldsets();
        $output = '<div class="t4-row-settings" style="display:none;" >';
        $output .= '<div class="t4-modal-overlay"></div>';
        $output .= '<div class="t4-modal t4-row-setting" data-target="#">';

        $output .= '<div class="t4-modal-header">';
        $output .= '<span class="t4-modal-header-title"><i class="fal fa-cog"></i>Row Options</span>';
        $output .= '<a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>';
        $output .= '</div>';

        $output .= '<div class="t4-modal-inner t4-row-inner"></div>';
        $output .= '<div class="t4-modal-content t4-modal-row">';
        $output .= '<ul class="nav nav-tabs mb-3" role="tablist">';
        $outputs = '<div class="tab-content" id="pills-tabContent">';
        // $options = array();
        foreach ($fieldsets as $key => $fieldset) {
            if (count($fieldsets) > 1) {
                $output .= self::renderFieldsetStart($fieldset);
            }
            $fields = $form->getFieldset($key);
            $fieldArray = array();
            foreach ($fields as $key => $field) {
                $group = $field->getAttribute('group') ? $field->getAttribute('group') : 'no-group';
                $type = $field->getAttribute('type');
                $filed_html = self::renderInputField($field, $group);
                $fieldArray[$group]['fields_html'][] = $filed_html;
            }
            $outputs .= self::renderGroups($fieldArray);
        }
        $outputs .= '</div>';
        $output .= '</ul>';
        $output .= $outputs;
        $output .= '</div>';
        $output .= '<div class="t4-modal-footer">';
        // $output .= '<a href="#" class="btn btn-primary btn-xs t4-settings-block-css"><span class="fal fa-edit"></span> Custom css</a>';
        $output .= '<a href="#" class="btn btn-secondary btn-xs t4-settings-cancel"><span class="fal fa-times"></span> Cancel</a>';
        $output .= '<a href="#" class="btn btn-success btn-xs t4-settings-apply" data-flag="row-setting"><span class="fal fa-check"></span> Apply</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public static function getColSettings()
    {
        $tplXml = T4PATH_TPL . '/templateDetails.xml';
        // new form
        $xmlfile = \T4\Helper\Path::findInTheme('params/layoutCol.xml');
        $form = JForm::getInstance('t4layoutCol', $xmlfile);
        // if ($xmlfile) $form->loadFile($xmlfile);
        $form = new T4form($form);
        //remove all fields from group 'params' and reload them again in right other base on template.xml
        $form->removeGroup('t4layoutCol');
        $form->removeGroup('t4layout');
        //load the template
        $form->loadFile($xmlfile);
        //overwrite / extend with params of template
        $form->loadFile($tplXml, true, '//layouts');

        $fieldsets = $form->getFieldsets();

        $output = '<div class="t4-cols-settings" style="display:none;">';
        $output .= '<div class="t4-modal-overlay"></div>';
        $output .= '<div class="t4-modal t4-cols-setting" data-target="#">';
        $output .= '<div class="t4-modal-header">';
        $output .= '<span class="t4-modal-header-title"><i class="fal fa-cog"></i>'.\JText::_('T4_LAYOUT_COL_SETTINGS').'</span>';
        $output .= '<span class="t4-modal-header-title t4-edit-block-title" style="display:none;"><i class="fal fa-cog"></i>Edit bLock</span>';
        $output .= '<a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>';
        $output .= '<a href="#" class="t4-modal-block-close"  style="display:none;"><span class="fal fa-times"></span></a>';
        $output .= '</div>';
        $output .= '<div class="t4-modal-inner t4-cols-inner">';
        $outputs = '<div class="tab-content t4-modal-content t4-modal-col" id="pills-tabContent">';

        foreach ($fieldsets as $key => $fieldset) {
            //only one tab show
            /*if(count($fieldsets) > 1){
                $output .= self::renderFieldsetStart($fieldset);
            }*/

            $fields = $form->getFieldset($key);

            $fieldArray = array();
            foreach ($fields as $key => $field) {
                $group = $field->getAttribute('group') ? $field->getAttribute('group') : 'no-group';
                $type = $field->getAttribute('type');
                $filed_html = self::renderInputField($field, $group);
              
                $fieldArray[$group]['fields_html'][] = $filed_html;
            }
            $outputs .= self::renderGroups($fieldArray);
        }
        $output .= $outputs;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="t4-modal-footer">';
        $output .= '<a href="#" class="btn btn-secondary btn-xs t4-settings-cancel"><span class="fal fa-times"></span> Cancel</a>';
        $output .= '<a href="#" class="btn btn-success btn-xs t4-settings-apply" data-flag="column-setting"><span class="fal fa-check"></span> Apply</a>';
        $output .= '</div>';
        $output .= '<div class="t4-modal-footer t4-edit-block-footer"  style="display:none;">';
        $output .= '<a href="#" class="btn btn-secondary btn-xs t4-edit-block-remove" data-local="0"><span class="fal fa-trash-alt"></span>Delete Block</a>';
        $output .= '<a href="#" class="btn btn-secondary btn-xs t4-edit-block-cancel"><span class="fal fa-times"></span> Cancel</a>';
        $output .= '<a href="#" class="btn btn-success btn-xs t4-edit-block-save"><span class="fal fa-check"></span> Save</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }
    public static function getMegaRowConfig()
    {
        $tplXml = T4PATH_TPL . '/templateDetails.xml';
        // new form
        $xmlfile = \T4\Helper\Path::findInTheme('params/megamenu.xml');
        $form = JForm::getInstance('megaRow', $xmlfile);
        // if ($xmlfile) $form->loadFile($xmlfile);
        $form = new T4form($form);
        //remove all fields from group 'params' and reload them again in right other base on template.xml
        $form->removeGroup('megaRow');
        //load the template
        $form->loadFile($xmlfile);
        //overwrite / extend with params of template
        $form->loadFile($tplXml, true, '//layouts');
        $fieldsets = $form->getFieldsets('megaRow');
        $output = '<div class="t4-mega-row-modal"  data-target="#" style="display:none;">';
        $output .= '<div class="t4-modal-overlay"></div>';
        $output .= '<div class="t4-modal t4-mega-row">';
        $output .= '<div class="t4-modal-header">';
        $output .= '<span class="t4-modal-header-title"><i class="fal fa-cog"></i>Menu row options</span>';
        $output .= '<a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>';
        $output .= '</div>';
        $output .= '<div class="t4-modal-inner t4-mega-inner">';
        $outputs = '<div class="tab-content t4-modal-content" id="pills-tabContent">';
        // $options = array();
        foreach ($fieldsets as $key => $fieldset) {
            if (count($fieldsets) > 1) {
                $output .= self::renderFieldsetStart($fieldset);
            }

            $fields = $form->getFieldset($key);

            $fieldArray = array();
            foreach ($fields as $key => $field) {
                $group = $field->getAttribute('group') ? $field->getAttribute('group') : 'no-group';
                $type = $field->getAttribute('type');
                $filed_html = self::renderInputField($field, $group);
              
                $fieldArray[$group]['fields_html'][] = $filed_html;
            }
            $outputs .= self::renderGroups($fieldArray);
        }
        $output .= $outputs;
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<div class="t4-modal-footer">';
        $output .= '<a href="#" class="btn btn-secondary btn-xs t4-settings-cancel"><span class="fal fa-times"></span> Cancel</a>';
        $output .= '<a href="#" class="btn btn-success btn-xs t4-menu-settings-apply" data-flag="mega-setting"><span class="fal fa-check"></span> Apply</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }
    public static function getMegaItemSettings()
    {
        $tplXml = T4PATH_TPL . '/templateDetails.xml';
        // new form
        $xmlfile = \T4\Helper\Path::findInTheme('params/megamenu.xml');
        $form = JForm::getInstance('megaCol', $xmlfile);
        // if ($xmlfile) $form->loadFile($xmlfile);
        $form = new T4form($form);
        //remove all fields from group 'params' and reload them again in right other base on template.xml
        $form->removeGroup('megaCol');
        //load the template
        $form->loadFile($xmlfile);
        //overwrite / extend with params of template
        $form->loadFile($tplXml, true, '//megamenu');
        $fieldsets = $form->getFieldsets('megaCol');
        $output = '<div class="t4-mega-item-modal" data-target="#" style="display:none;">';
        $output .= '<div class="t4-modal-overlay"></div>';
        $output .= '<div class="t4-modal t4-mega-item">';
        $output .= '<div class="t4-modal-header">';
        $output .= '<span class="t4-modal-header-title"><i class="fal fa-cog"></i>Menu Item options</span>';
        $output .= '<a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>';
        $output .= '</div>';
        $output .= '<div class="t4-modal-inner t4-mega-item-inner">';
        $outputs = '<div class="tab-content t4-modal-content" id="pills-tabContent">';
        // $options = array();
        foreach ($fieldsets as $key => $fieldset) {
            if (count($fieldsets) > 1) {
                $output .= self::renderFieldsetStart($fieldset);
            }
            $fields = $form->getFieldset($key);

            $fieldArray = array();
            foreach ($fields as $key => $field) {
                $group = $field->getAttribute('group') ? $field->getAttribute('group') : 'no-group';
                $type = $field->getAttribute('type');
                $filed_html = self::renderInputField($field, $group);
              
                $fieldArray[$group]['fields_html'][] = $filed_html;
            }
            $outputs .= self::renderGroups($fieldArray);
        }
        $output .= $outputs;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="t4-modal-footer">';
        $output .= '<a href="#" class="btn btn-secondary btn-xs t4-settings-cancel"><span class="fal fa-times"></span> Cancel</a>';
        $output .= '<a href="#" class="btn btn-success btn-xs t4-menu-settings-apply" data-flag="item-setting"><span class="fal fa-check"></span> Apply</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }
    public static function renderFieldsetStart($fieldset)
    {
        $html  = '<li class="nav-item t4-group t4-group-'.$fieldset->name.'">';
        $html .= '<a class="nav-link" id="'.$fieldset->name.'-tab" data-toggle="pill" href="#'.$fieldset->name.'" role="tab" aria-controls="'.$fieldset->name.'" aria-selected="false">'. \JText::_($fieldset->label) .'</a>';
        $html .= '</li>';

        return $html;
    }
    public static function renderFieldsetEnd()
    {
        return '</div></div>';
    }


    public static function renderGroups($groups)
    {
        $html = '';
        foreach ($groups as $key => $group) {
            if ($key != 'no-group') {
                $html .= self::renderGroupStart($key);
            }

            $html .= self::getFields($group['fields_html']);

            if ($key != 'no-group') {
                $html .= self::renderGroupEnd();
            }
        }

        return $html;
    }

    public static function renderGroupStart($group)
    {
        if ($group == 'col') {
            $group = 'general';
        }
        $html  = '<div id="'.$group.'" class="tab-pane t4-group-list-'.$group.' active">';
        $html .= '<div class="row">';

        return $html;
    }

    public static function renderGroupEnd()
    {
        return '</div></div>';
    }

    public static function getFields($fields)
    {
        $html = '';
        foreach ($fields as $field) {
            $html .= $field;
        }

        return $html;
    }

    public static function renderInputField($field = '', $group = '')
    {
        $showon = $field->getAttribute('showon');
        $attribs = '';
        $field_html = '';
        $field_html .= '<div class="control-group ' . $field->getAttribute('class_field') . '"'. $attribs .'>';
        
        $field_html .= '<div class="control-group-inner">';
        if (!$field->getAttribute('hideLabel')) {
            $field_html .= '<div class="control-label">' . str_replace('</label>', ' <span class="hasTooltip fal fa-question-circle"></span></label>', $field->label) .'</div>';
        }
        $field_html .= '<div class="controls">';
        $field_html .= $field->input;
        $field_html .= '</div>';
        $field_html .= '</div>';
        $field_html .= '</div>';

        return $field_html;
    }
}
