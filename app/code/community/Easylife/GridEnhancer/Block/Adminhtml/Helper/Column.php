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
 * field renderer for position setting
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_Helper_Column
    extends Varien_Data_Form_Element_Select {
    /**
     * get select values
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getValues(){
        return $this->getData('values');
    }
    /**
     * get html for position select
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getPositionField(){
        $html = '';
        $label = Mage::helper('easylife_gridenhancer')->__('After');
        $html .= '<span class="position">';
        $html .= '<label for="' . $this->getHtmlId() . '_position"'
            . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
        $html .= '<select'
            . ' name="' . parent::getName() . '[position]" value="'.$this->getPosition().'" class="input-text field-position'.(($this->getRequired())?' required-entry':'').'"'
            . ' id="' . $this->getHtmlId() . '_position"' . ($this->getDisabled() ? ' disabled="disabled"': '')
            . '></select>';
        $html .= '</span>';
        return $html;
    }
    /**
     * get html for delete button
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getDeleteButton(){
        $button = Mage::app()->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'class' => 'delete'
            ));
        return $button->toHtml();

    }

    /**
     * get html for the element
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getElementHtml() {
        $html = parent::getElementHtml();
        $html .= $this->_getPositionField();
        $html .= $this->_getDeleteButton();
        return $html;
    }
}