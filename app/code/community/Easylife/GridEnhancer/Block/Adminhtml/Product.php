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
 * form container
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_Product
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'easylife_gridenhancer';
        $this->_controller = 'adminhtml';
        $this->_mode = 'product';
        $this->setTemplate('easylife_gridenhancer/product.phtml');
    }

    /**
     * get the settings that are edited
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSettings(){
        return Mage::registry('current_settings');
    }

    /**
     * get header text
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getHeaderText(){
        return Mage::helper('easylife_gridenhancer')->__('Configure products grid');
    }

    /**
     * add buttons
     * @access protected
     * @return Easylife_GridEnhancer_Block_Adminhtml_Product
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _prepareLayout(){
        $this->setChild('reset_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('easylife_gridenhancer')->__('Reset'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );
        $this->setChild('save_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('easylife_gridenhancer')->__('Save'),
                    'onclick'   => 'editForm.submit()',
                    'class' => 'save'
                ))
        );
        $this->setChild('delete',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('easylife_gridenhancer')->__('Delete configuration'),
                    'class' => 'delete',
                    'onclick'=>'deleteConfirm(\''. Mage::helper('easylife_gridenhancer')->__('Are you sure you want to do this?')
                        .'\', \'' . $this->getDeleteUrl() . '\')'
                ))
        );
        $this->setChild('add_field',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('easylife_gridenhancer')->__('Add column'),
                    'class' => 'add add-field'
                ))
        );
        if ($this->_blockGroup && $this->_controller && $this->_mode) {
            $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form'));
        }
        return $this;
    }

    /**
     * get the cancel button html
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getCancelButtonHtml(){
        return $this->getChildHtml('reset_button');
    }
    /**
     * get the save button html
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSaveButtonHtml(){
        return $this->getChildHtml('save_button');
    }
    /**
     * get the add field button html
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAddFieldButtonHtml(){
        return $this->getChildHtml('add_field');
    }
    /**
     * get the delete button html
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getDeleteButtonHtml(){
        return $this->getChildHtml('delete');
    }

    /**
     * get grid config
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getConfig(){
        $config = array();
        $config['system_attributes']    = Mage::helper('easylife_gridenhancer/product')->getSystemAttributes(true);
        $config['position_identifier']  = '.field-position';
        $config['field_identifier']     = '.grid-field';
        $config['delete_identifier']    = 'button.delete';
        $config['add_field_identifier'] = '.add-field';
        $config['current']              = $this->getSettings()->getConfiguration();
        $config['field_template']       = 'field_template';
        $config['main_field']           = 'fields';
        $config['all_options']          = Mage::helper('easylife_gridenhancer/product')->getProductAttributes(true);
        $config['notice_at']            = 5;//TODO: make this configurable
        $config['notice_message']       = Mage::helper('easylife_gridenhancer')->__("Hold on there buddy!\nDon't you think that's enough?\nYou will make the grid unreadable!");

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * get field template
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getFieldTemplate(){
        $className = Mage::getConfig()->getBlockClassName('easylife_gridenhancer/adminhtml_helper_product_column');
        $element = new $className();
        //dummy form
        $form = new Varien_Data_Form();
        $element->setForm($form);
        $element->setName('field[{{id}}]');
        $element->setLabel(Mage::helper('easylife_gridenhancer')->__('Attribute %s', '{{id1}}'));
        $element->setClass('grid-field');
        $element->setRequired(true);
        $element->setId('field_{{id}}');
        $element->setHtmlContainerId('field_container_{{id}}');
        $element->setContainerId('field_container1_{{id}}');
        $container = Mage::app()->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element');
        $container->setElement($element);
        return $container->render($element);
    }

    /**
     * get the delete url
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getDeleteUrl(){
        return Mage::helper('adminhtml')->getUrl('adminhtml/gridenhancer_product/delete');
    }
}