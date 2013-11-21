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
 * product observer
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Model_Observer_Product
    extends Easylife_GridEnhancer_Model_Observer_Abstract {
    /**
     * get helper name
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getHelperName(){
        return Mage::getConfig()->getNode(Easylife_GridEnhancer_Helper_Product::getHelperPath());
    }
    /**
     * add selected attributes to collection
     * @access public
     * @param $observer
     * @return Easylife_GridEnhancer_Model_Observer_Product
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function addAttributesToCollection($observer){
        if ($this->_getHelper()->getIsDisabled()){
            return $this;
        }
        $collection = $observer->getEvent()->getCollection();
        $settings = $this->_getAdminSettings();
        if ($settings->getId()){
            $configuration = $settings->getConfiguration();
            if (is_array($configuration)){
                $collection->addAttributeToSelect(array_keys($configuration));
            }
        }
        return $this;
    }
}