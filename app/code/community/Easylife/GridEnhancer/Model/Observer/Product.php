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
class Easylife_GridEnhancer_Model_Observer_Product extends Varien_Object {
    /**
     * cache current admin settings
     * @var mixed
     */
    protected $_adminSettings = null;

    /**
     * add selected attributes to collection
     * @access public
     * @param $observer
     * @return Easylife_GridEnhancer_Model_Observer_Product
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function addAttributesToCollection($observer){
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
    /**
     * add columns to grid and manage button to container
     * @access public
     * @param $observer
     * @return Easylife_GridEnhancer_Model_Observer_Product
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function addColumnsToGrid($observer){
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid){
            $settings = $this->_getAdminSettings();
            $configuration = $settings->getConfiguration();
            if (is_array($configuration)){
                $block->setColumnFilters(array('multilevel-dropdown'=>'easylife_gridenhancer/adminhtml_helper_grid_column_multilevel'));
                foreach ($configuration as $attribute=>$after){
                    $attributeInstance = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute);
                    if ($attributeInstance->getId()){
                        $columnConfig = $this->_getColumnConfig($attributeInstance);
                        if ($columnConfig){
                            $block->addColumnAfter($attribute, $columnConfig, $after);
                        }
                    }
                }
            }
        }
        elseif ($block instanceof Mage_Adminhtml_Block_Catalog_Product){
            if (Mage::getSingleton('admin/session')->isAllowed('catalog/products/easylife_gridenhancer')){
                $block->addButton('manage', array(
                    'label' => Mage::helper('easylife_gridenhancer')->__('Manage Columns'),
                    'onclick' => "setLocation('".Mage::helper('adminhtml')->getUrl('adminhtml/gridenhancer_product')."')"
                ));
            }
        }
    }

    /**
     * get the admin settings
     * @access protected
     * @return mixed|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getAdminSettings(){
        if (is_null($this->_adminSettings)){
            $this->_adminSettings = $settings = Mage::getModel('easylife_gridenhancer/settings')
                ->loadByAdmin(Mage::getSingleton('admin/session')->getUser()->getId());
        }
        return $this->_adminSettings;
    }

    /**
     * get collumn config
     * @access protected
     * @param $attribute
     * @return array|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getColumnConfig($attribute){
        $type = $attribute->getFrontendInput();
        $config = array();
        $config['header'] = $attribute->getFrontendLabel();
        $config['index'] = $attribute->getAttributeCode();
        $store = $this->_getStore();
        switch($type){
            case 'text':
            //intentional fall through
            case 'weight':
            //intentional fall through
            case 'textarea':
                $config['type'] = 'text';
                if ($attribute->getBackendType() == 'int' || $attribute->getBackendType() == 'decimal' ){
                    $config['type'] = 'number';
                }
                break;
            case 'select':
                $config['type'] = 'options';
                $options = $attribute->getSource()->getAllOptions(false);
                $clear = true;
                foreach ($options as $option){
                    if (is_array($option['value'])){
                        $clear = false;
                    }
                }
                if ($clear){
                    $config['options'] = $this->_getOptions($options);
                }
                else {
                    $config['options'] = $options;
                    $config['type'] = 'multilevel-dropdown';
                    $config['with_empty'] = true;
                }
                if ($attribute->usesSource()){
                    $source = $attribute->getSource();
                    $reflector = new ReflectionMethod(get_class($source), 'addValueSortToCollection');
                    //if the source model does not override the method `addValueSortToCollection` sorting won't work
                    //the code below may return false positives if the method is defined in the source class but just calls parent::addValueSortToCollection or returns $this.
                    //any other ideas on how approach this?
                    if ($reflector->getDeclaringClass()->getName() == 'Mage_Eav_Model_Entity_Attribute_Source_Abstract'){
                        $config['sortable'] = false;
                    }
                }
                break;
            case 'price':
                $config['type']  = 'price';
                $config['currency_code'] = $store->getBaseCurrency()->getCode();
                break;
            case 'date':
                $config['type'] = 'date';
            break;
            default :
                //not all columns are supported
                return null;
        }
        return $config;
    }

    /**
     * transform options
     * @access protected
     * @param $arr
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getOptions($arr){
        $options = array();
        foreach ($arr as $item){
            $options[$item['value']] = $item['label'];
        }
        return $options;
    }

    /**
     * get current selected store
     * @access public
     * @return Mage_Core_Model_Store
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getStore() {
        $storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
}