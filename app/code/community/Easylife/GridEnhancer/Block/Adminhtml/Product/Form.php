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
 * form for column settings
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_Product_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Easylife_GridEnhancer_Block_Adminhtml_Product_Form
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getUrl('adminhtml/gridenhancer_product/save'), 'method' => 'post')
        );
        $this->setForm($form);
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('import_form', array('legend'=>Mage::helper('easylife_gridenhancer')->__('Configure products grid')));
        $fieldset->addField('fields', 'hidden', array(
            'name'      =>'fields',
        ));
        return parent::_prepareForm();
    }
}