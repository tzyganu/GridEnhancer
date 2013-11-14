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
 * product controller
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Adminhtml_Gridenhancer_ProductController
    extends Mage_Adminhtml_Controller_Action {
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
     * edit action
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function editAction(){
        $settings = Mage::getModel('easylife_gridenhancer/settings')->loadByAdmin(Mage::getSingleton('admin/session')->getUser()->getId());
        Mage::register('current_settings', $settings);
        $this->loadLayout();
        $this->renderLayout();
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
            $settings = Mage::getModel('easylife_gridenhancer/settings')->loadByAdmin(Mage::getSingleton('admin/session')->getUser()->getId());
            $settings->setConfiguration($fields);
            $settings->setAdminId(Mage::getSingleton('admin/session')->getUser()->getId());
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
        $settings = Mage::getModel('easylife_gridenhancer/settings')->loadByAdmin(Mage::getSingleton('admin/session')->getUser()->getId());
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
     * check access
     * @access protected
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')
            ->isAllowed('catalog/products/easylife_gridenhancer');
    }
}