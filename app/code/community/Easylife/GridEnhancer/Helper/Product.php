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
 * product helper
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Helper_Product extends Mage_Core_Helper_Abstract {
    /**
     * path to system attributes
     */
    const XML_SYSTEM_ATTRIBUTES_PATH = 'global/gridenhancer/system_attributes/product';
    /**
     * path to allowed types
     */
    const XML_ALLOWED_TYPES_PATH = 'global/gridenhancer/allowed_types/product';
    /**
     * cache product attributes
     * @var mixed
     */
    protected $_productAttributes = null;

    /**
     * get product attributes
     * @access public
     * @param bool $withEmpty
     * @return mixed|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getProductAttributes($withEmpty = true){
        if (is_null($this->_productAttributes)){
            $collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('attribute_code', array('nin'=>$this->getSystemAttributes()))
                ->addFieldToFilter('frontend_input', array('in'=>$this->getAllowedTypes()))
                ->setOrder('frontend_label', 'asc');
            ;
            foreach ($collection as $attribute){
                $this->_productAttributes[] = array(
                    'label'=>$attribute->getFrontendLabel(),
                    'value'=>$attribute->getAttributeCode()
                );
            }
        }
        $attributes = $this->_productAttributes;
        if ($withEmpty){
            array_unshift($attributes, array('label'=>'','value'=>''));
        }
        return $attributes;
    }

    /**
     * get system attributes
     * @access public
     * @param bool $withNames
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSystemAttributes($withNames = false){
        $codes = (array)Mage::getConfig()->getNode(self::XML_SYSTEM_ATTRIBUTES_PATH);
        if (!$withNames){
            return array_keys($codes);
        }
        $attributes = array();
        foreach ($codes as $code => $data){
            $attributes[$code] = (string)$data->label;
        }
        return $attributes;
    }

    /**
     * get allowed types
     * access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAllowedTypes(){
        return array_keys((array)Mage::getConfig()->getNode(self::XML_ALLOWED_TYPES_PATH));
    }
}