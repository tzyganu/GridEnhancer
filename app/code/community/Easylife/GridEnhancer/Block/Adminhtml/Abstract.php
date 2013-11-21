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
 * general admin block
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_Abstract
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * path to notice config
     */
    const XML_NOTICE_PATH = 'easylife_gridenhancer/settings/notice';
    /**
     * block helper
     * @var Easylife_GridEnhancer_Helper_Abstract
     */
    protected $_helper;
    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function __construct(){
        parent::__construct();
        $this->_objectId   = 'mode';
        $this->_blockGroup = $this->getBlockGroup();
        $this->_controller = $this->getControllerName();
        $this->_mode = $this->getMode();
    }

    /**
     * get helper asociated to the block
     * @access public
     * @return Easylife_GridEnhancer_Helper_Abstract
     * @throws Easylife_GridEnhancer_Exception
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridHelper(){
        if (is_null($this->_helper)){
            $mode = $this->getHelperName();
            if (empty($mode)){
                throw new Easylife_GridEnhancer_Exception(Mage::helper('easylife_gridenhancer')->__('Helper not set'));
            }
            $helperPath = Easylife_GridEnhancer_Helper_Abstract::XML_ROOT_PATH.$mode.'/helper';
            $helperAlias = Mage::getConfig()->getNode($helperPath);
            $helper = Mage::helper($helperAlias);
            if (!$helper){
                throw new Easylife_GridEnhancer_Exception(Mage::helper('easylife_gridenhancer')->__('Helper not set'));
            }
            $this->_helper = $helper;
        }
        return $this->_helper;
    }

    /**
     * get block group
     * can be overwritten in children
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getBlockGroup(){
        return 'easylife_gridenhancer';
    }
    /**
     * get controller name
     * can be overwritten in children
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getControllerName(){
        return 'adminhtml';
    }

    /**
     * get system attributes
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSystemAttributes(){
        return $this->getGridHelper()->getSystemAttributes(null, true);
    }
    /**
     * get al available options
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAllOptions(){
        return $this->getGridHelper()->getAttributes(true);
    }
    /**
     * get save url
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSaveActionUrl(){
        return $this->getGridHelper()->getSaveActionUrl();
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
     * get header text for the form
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getHeaderText(){
        return $this->getGridHelper()->getHeaderText();
    }
    /**
     * add buttons
     * @access protected
     * @return Easylife_GridEnhancer_Block_Adminhtml_Abstract
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _beforeToHtml(){
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
        if ($this->_blockGroup && $this->_controller) {
            $form = $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . 'abstract_form');
            $form->setActionUrl($this->getSaveActionUrl());
            $form->setFieldsetLegend($this->getHeaderText());
            $form->setMode($this->getMode());
            $this->setChild('form', $form);
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
        $config['system_attributes']    = $this->getSystemAttributes();
        $config['position_identifier']  = '.field-position';
        $config['field_identifier']     = '.grid-field';
        $config['delete_identifier']    = 'button.delete';
        $config['add_field_identifier'] = '.add-field';
        $config['current']              = $this->getSettings()->getConfiguration();
        $config['field_template']       = 'field_template';
        $config['main_field']           = 'fields';
        $config['all_options']          = $this->getAllOptions();
        $config['notice_at']            = (int)Mage::getStoreConfig(self::XML_NOTICE_PATH, 0);
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
        $className = Mage::getConfig()->getBlockClassName('easylife_gridenhancer/adminhtml_helper_column');
        //instantiate directly
        //createBlock allows only Mage_Core_Block_Abstract children to be instantiated
        $element = new $className();
        $form = new Varien_Data_Form();
        $element->setForm($form);
        $element->setName('field[{{id}}]');
        $element->setLabel(Mage::helper('easylife_gridenhancer')->__('Attribute %s', '{{id1}}'));
        $element->setClass('grid-field');
        $element->setRequired(true);
        $element->setId('field_{{id}}');
        $element->setHtmlContainerId('field_container_{{id}}');
        $element->setContainerId('field_container1_{{id}}');
        $element->setValues($this->getAllOptions());
        $container = Mage::app()->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element');
        $container->setElement($element);
        return $container->render($element);
    }
}