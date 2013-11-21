<?php
/**
 * Easylife_GridEnhancer extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_GRID_ENHANCER.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Easylife
 * @package        Easylife_GridEnhancer
 * @copyright      Copyright (c) 2013
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * config section renderer
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_System_Config_Form_Fieldset_Disable
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {
    /**
     * dummy for element
     * @var Varien_Object
     */
    protected $_dummyElement;
    /**
     * field renderer
     * @var Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected $_fieldRenderer;
    /**
     * yes/no values
     * @var array
     */
    protected $_values;

    /**
     * render the element
     * @access public
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = $this->_getHeaderHtml($element);
        $nodes = (array)Mage::app()->getConfig()->getNode('global/gridenhancer');
        foreach ($nodes as $nodeName=>$settings) {
            $label = (string)$settings->label;
            $html.= $this->_getFieldHtml($element, $nodeName, $label);
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    /**
     * create a dummy element
     * @access protected
     * @return Varien_Object
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getDummyElement() {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default'=>0, 'show_in_website'=>0));
        }
        return $this->_dummyElement;
    }

    /**
     * get field renderer
     * @access protected
     * @return Mage_Adminhtml_Block_System_Config_Form_Field|object
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getFieldRenderer() {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    /**
     * get the yes/no values
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getValues() {
        if (empty($this->_values)) {
            $this->_values = array(
                array('label'=>Mage::helper('adminhtml')->__('No'), 'value'=>0),
                array('label'=>Mage::helper('adminhtml')->__('Yes'), 'value'=>1),
            );
        }
        return $this->_values;
    }

    /**
     * render one field
     * @access protected
     * @param $fieldset
     * @param $nodeName
     * @param $label
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getFieldHtml($fieldset, $nodeName, $label) {
        $configData = $this->getConfigData();
        $path = 'easylife_gridenhancer/disable/'.$nodeName;
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (int)(string)$this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }
        $e = $this->_getDummyElement();

        $field = $fieldset->addField($nodeName, 'select',
            array(
                'name'          => 'groups[disable][fields]['.$nodeName.'][value]',
                'label'         => $label,
                'value'         => $data,
                'values'        => $this->_getValues(),
                'inherit'       => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            ))->setRenderer($this->_getFieldRenderer());
        return $field->toHtml();
    }
}