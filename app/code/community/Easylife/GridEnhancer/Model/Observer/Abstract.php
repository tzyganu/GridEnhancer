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
 * general observer
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
abstract class Easylife_GridEnhancer_Model_Observer_Abstract extends Varien_Object {
    /**
     * cache current admin settings
     * @var mixed
     */
    protected $_adminSettings = null;

    /**
     * get helper name
     * @access protected
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected abstract function _getHelperName();
    /**
     * get grid identifier
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridIdentifier(){
        return $this->_getHelper()->getGridIdentifier();
    }
    /**
     * get grid container type
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridContainerBlockType(){
        return (string)$this->_getHelper()->getConfigData('container_type');
    }
    /**
     * get acl for manage columns
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getManageColumnsAcl(){
        return $this->_getHelper()->getAclPath();
    }
    /**
     * get url to manage grid
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getManageGridUrl(){
        return Mage::helper('adminhtml')->getUrl(
            (string)$this->_getHelper()->getConfigData('manage_url')
        );
    }
    /**
     * get helper
     * @access protected
     * @return Mage_Core_Helper_Abstract
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getHelper(){
        return Mage::helper($this->_getHelperName());
    }
    /**
     * get urgrid block type
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridBlockType(){
        return $this->getGridContainerBlockType().'_Grid';
    }

    /**
     * add columns to grid and manage button to container
     * @access public
     * @param $observer
     * @return Easylife_GridEnhancer_Model_Observer_Abstract
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function addColumnsToGrid($observer){
        if ($this->_getHelper()->getIsDisabled()){
            return $this;
        }
        //if ($this->_getHelper()->validateRequest()){
            $block = $observer->getEvent()->getBlock();
            $blockType = $this->getGridBlockType();
            $containerType = $this->getGridContainerBlockType();
            if ($block instanceof $blockType){
                $block->setColumnFilters(array('multilevel-dropdown'=>'easylife_gridenhancer/adminhtml_helper_grid_column_multilevel'));
                $this->_addColumns($block);
            }
            elseif ($block instanceof $containerType){
                if (Mage::getSingleton('admin/session')->isAllowed($this->getManageColumnsAcl())){
                    $block->addButton($this->getButtonIdentifier(), array(
                        'label' => $this->getButtonLabel(),
                        'onclick' => "setLocation('".$this->_getManageGridUrl()."')"
                    ));
                }
            }
        //}
    }

    /**
     * get button name
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getButtonIdentifier(){
        return 'manage';
    }
    public function getButtonLabel(){
        return Mage::helper('easylife_gridenhancer')->__('Manage Columns');
    }
    /**
     * get button label
     * @access protected
     * @param $block
     * @return Easylife_GridEnhancer_Model_Observer_Abstract
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     * //TODO: maybe refactor this. Split into 2 methods, one for each type
     */
    protected function _addColumns($block){
        $settings = $this->_getAdminSettings();
        $configuration = $settings->getConfiguration();

        if (is_array($configuration)){
            foreach ($configuration as $attribute=>$after){
                $parts = explode('__', $attribute, 2);
                if (count($parts) != 2){
                    continue;
                }

                if ($this->_getHelper()->getType() == 'eav'){

                    $attributeInstance = Mage::getModel('eav/config')->getAttribute($parts[0], $parts[1]);
                    if ($attributeInstance->getId()){
                        $entityType = $this->_getHelper()->getEntityType($parts[0]);
                        if ($entityType){
                            $columnIndex = $entityType['column_prefix'].$parts[1];
                        }
                        else{
                            $columnIndex = $parts[1];
                        }
                        $columnConfig = $this->_getColumnConfig($attributeInstance, $columnIndex);
                        if ($columnConfig){
                            $after = $this->_getAfter($after);
                            $block->addColumnAfter($columnIndex, $columnConfig, $after);
                        }
                    }
                }
                elseif ($this->_getHelper()->getType() == 'flat'){
                    $entityType = $this->_getHelper()->getEntityType($parts[0]);
                    if ($entityType){
                        $columnIndex = $entityType['column_prefix'].$parts[1];
                    }
                    else{
                        $columnIndex = $parts[1];
                    }
                    $columnConfig = $this->_getFlatColumnConfig($parts[1]);
                    if ($columnConfig){
                        $after = $this->_getAfter($after);
                        $block->addColumnAfter($columnIndex, $columnConfig, $after);
                    }
                }
            }
            $block->sortColumnsByOrder();
        }
        return $this;
    }

    /**
     * process the after column value
     * @access protected
     * @param $after
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getAfter($after){
        $parts = explode('__', $after, 2);
        if (count($parts) != 2){
            return '';
        }
        //check if the column is system
        $path = $this->_getHelper()->getXmlSystemAttributesPath();
        $config = Mage::getConfig()->getNode($path.'/'.$parts[1]);
        if ($config){
            if (!empty($config->column)){
                return (string)$config->column;
            }
            return $parts[1];
        }
        //check if it has column prefix
        $entityType = $this->_getHelper()->getEntityType($parts[0]);
        if ($entityType){
            if(isset($entityType['column_prefix'])){
                return $entityType['column_prefix'].$parts[1];
            }
        }
        return $parts[1];
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
                    $this->getGridIdentifier()
                );
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
    protected function _getColumnConfig($attribute, $columnIndex = null){
        $type = $attribute->getFrontendInput();
        $config = array();
        $config['header'] = $attribute->getFrontendLabel();
        if (is_null($columnIndex)){
            $config['index'] = $attribute->getAttributeCode();
        }
        else{
            $config['index'] = $columnIndex;
        }
        $store = $this->_getStore();
        switch($type){
            case 'text':
                //intentional fall through
            case 'weight':
                //intentional fall through
            case 'multiline':
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
     * get column config for flat columns
     * @access protected
     * @param $attribute
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function _getFlatColumnConfig($attribute){
        $options = $this->_getHelper()->getConfigData('allowed_attributes/'.$attribute);
        $config = array();
        $config['header'] = $options->label;
        $config['type'] = $options->type;
        if ($options->type == 'options'){
            if ((string)$options->source_model){
                $config['options'] = $this->_getOptions(Mage::getSingleton((string)$options->source_model)->toOptionArray());
            }
            else {
                $source = (array)$options->source;
                $values = array();
                foreach ($source as $value=>$_data){
                    if (isset($_data->value)){
                        $value = (string)$_data->value;
                    }
                    $values[$value] = (string)$_data->label;
                }
                $config['options'] = $values;
            }
        }
        $config['index'] = $attribute;
        if (isset($options->filter)){
            $config['filter'] = (bool)(string)$options->filter;
        }
        if (isset($options->sortable)){
            $config['sortable'] = (bool)(string)$options->sortable;
        }
        //echo "<pre>"; print_r($config);exit;
        return $config;
    }
}