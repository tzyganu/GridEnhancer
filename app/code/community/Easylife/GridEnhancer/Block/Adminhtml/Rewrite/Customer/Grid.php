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
 * rewrite customer grid
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Block_Adminhtml_Rewrite_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid {
    /**
     * the admin settings for the grid
     * @var mixed
     */
    protected $_adminSettings;
    /**
     * get the admin settings
     * @access protected
     * @return mixed|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getAdminSettings(){
        if (is_null($this->_adminSettings)){
            $this->_adminSettings = $settings = Mage::getModel('easylife_gridenhancer/settings')
                ->loadByAdmin(
                    Mage::getSingleton('admin/session')->getUser()->getId(),
                    Easylife_GridEnhancer_Helper_Customer::CUSTOMER_GRID_IDENTIFIER
                );
        }
        return $this->_adminSettings;
    }

    /**
     * rewrite prepare page
     * this looks like the cleanest way to add other attributes to collection
     * without rewriting prepareCollection
     *
     * @access protected
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _preparePage(){
        if (!Mage::helper('easylife_gridenhancer/customer')->getIsDisabled()) {
            $collection = $this->getCollection();
            $settings = $this->_getAdminSettings();
            if ($settings->getId()){
                $configuration = $settings->getConfiguration();
                if (is_array($configuration)){
                    foreach ($configuration as $attribute=>$position){
                        $parts = explode('__', $attribute, 2);
                        if (count($parts) != 2){
                            continue;
                        }
                        if ($parts[0] == 'customer'){
                            $collection->addAttributeToSelect($parts[1]);
                        }
                        elseif ($parts[0] == 'customer_address'){
                            //check if attribute is already joined
                            //again - ugly way of doing it
                            //but I cannot access it in a different way
                            $reflection = new ReflectionClass($collection);
                            $property = $reflection->getProperty('_joinAttributes');
                            $property->setAccessible(true);
                            $value = $property->getValue($collection);
                            if (!isset($value['billing_'.$parts[1]])){
                                $collection->joinAttribute('billing_'.$parts[1], 'customer_address/'.$parts[1], 'default_billing', null, 'left');
                            }
                        }
                    }
                }
            }
        }
        return parent::_preparePage();
    }
}