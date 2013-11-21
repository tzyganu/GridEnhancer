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
 * controller for handling the grid management
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Adminhtml_GridEnhancer_IndexController
    extends Mage_Adminhtml_Controller_Action {
    /**
     * grid identifier
     * @var string
     */
    protected $_mode;
    /**
     * flag for checking if the management is allowed
     * @var bool
     */
    protected $_notAllowed = false;

    /**
     * check if we have access
     * @access public
     * @return Mage_Adminhtml_Controller_Action
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function preDispatch(){
        $mode = $this->getRequest()->getParam('mode');
        if (empty($mode)){
            $this->_notAllowed = true;
        }
        else {
            $this->_mode = $mode;
            try{
                $helper = $this->_getHelper();
            }
            catch (Exception $e){
                $this->_notAllowed = true;
            }
        }
        if ($this->_getHelper()->getIsDisabled()){
            $this->_notAllowed = true;
        }

        return parent::preDispatch();
    }

    /**
     * default action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function indexAction(){
        $this->_forward('edit');
    }

    /**
     * get the acl path
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAclPath(){
        return $this->_getHelper()->getAclPath();
    }

    /**
     * get the identifier of the helper
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getHelperName(){
        return (string)Mage::getConfig()->getNode(
            Easylife_GridEnhancer_Helper_Abstract::XML_ROOT_PATH.$this->_mode.'/helper'
        );
    }

    /**
     * get current grid
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridIdentifier(){
        return $this->_getHelper()->getGridIdentifier();
    }
    /**
     * get helper associated to controller
     * @access protected
     * @return Mage_Adminhtml_Helper_Data|Mage_Core_Helper_Abstract
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getHelper(){
        return Mage::helper($this->_getHelperName());
    }

    /**
     * check access
     * @access protected
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _isAllowed(){
        return !$this->_notAllowed && Mage::getSingleton('admin/session')
            ->isAllowed($this->getAclPath());
    }
    /**
     * get current grid
     * @access protected
     * @return Easylife_GridEnhancer_Model_Settings
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _initSettings(){
        return $this->_getHelper()->getCurrentSettings();
    }
    /**
     * save action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function saveAction(){
        $fields = $this->getRequest()->getPost('fields');
        try{
            if (empty($fields)){
                throw new Mage_Core_Exception(
                    Mage::helper('easylife_gridenhancer')->__('Configuration cannot be empty')
                );
            }
            $settings = $this->_initSettings();
            $settings->setConfiguration($fields);
            $settings->setAdminId(Mage::getSingleton('admin/session')->getUser()->getId())
                ->setGridCode($this->getGridIdentifier());
            $settings->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('easylife_gridenhancer')->__('Configuration was saved'));
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }
    /**
     * delete action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function deleteAction(){
        $settings = $this->_initSettings();
        try{
            if ($settings->getId()){
                $settings->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('easylife_gridenhancer')->__('Configuration was deleted'));
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }
    /**
     * edit action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function editAction(){
        $settings = $this->_initSettings();
        Mage::register('current_settings', $settings);
        if ($message = $this->_getHelper()->getMessage()){
            Mage::getSingleton('adminhtml/session')->addNotice($message);
        }
        $this->loadLayout();
        $this->_setActiveMenu($this->_getHelper()->getMenuPath());
        $this->getLayout()->getBlock('manage_grid')->setHelperName($this->_mode);
        $this->renderLayout();
    }
}