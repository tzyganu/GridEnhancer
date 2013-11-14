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
 * filter renderer for "special" fields
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_Helper_Grid_Column_Multilevel
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract {
    /**
     * get html of the filter
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getHtml(){
        $options = $this->getOptions();
        if ($this->getColumn()->getWithEmpty()) {
            array_unshift($options, array(
                'value' => '',
                'label' => ''
            ));
        }
        $html = sprintf('<select name="%s" id="%s" class="no-changes">', $this->_getHtmlName(), $this->_getHtmlId())
            . $this->_drawOptions($options)
            . '</select>';
        return $html;
    }

    /**
     * draw options recursive
     * @access protected
     * @param $options
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _drawOptions($options) {
        if (empty($options) || !is_array($options)) {
            return '';
        }
        $value = $this->getValue();
        $html  = '';

        foreach ($options as $option) {
            if (!isset($option['value']) || !isset($option['label'])) {
                continue;
            }
            if (is_array($option['value'])) {
                $html .= '<optgroup label="'.$option['label'].'">'
                    . $this->_drawOptions($option['value'])
                    . '</optgroup>';
            } else {
                $selected = (($option['value'] == $value && (!is_null($value))) ? ' selected="selected"' : '');
                $html .= '<option value="'.$option['value'].'"'.$selected.'>'.$option['label'].'</option>';
            }
        }

        return $html;
    }

    /**
     * get available options
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getOptions() {
        return $this->getColumn()->getOptions();
    }
}