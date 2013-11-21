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
 * abstract helper
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
abstract class Easylife_GridEnhancer_Helper_Abstract
    extends Mage_Core_Helper_Abstract {
    /**
     * wildcard
     */
    const WILDCARD = '*';
    /**
     * root path
     */
    const XML_ROOT_PATH = 'global/gridenhancer/';
    /**
     * all attributes
     * @var mixed
     */
    protected $_attributes = null;
    /**
     * system attributes by entity type
     * @var mixed
     */
    protected $_systemAttributes = null;
    /**
     * all system attribtues
     * @var mixed
     */
    protected $_allSystemAttributes = null;

    /**
     * get grid identifier
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public abstract function getGridIdentifier();

    /**
     * check if grid management is disabled
     * @access public
     * @return bool
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getIsDisabled(){
        return Mage::getStoreConfigFlag('easylife_gridenhancer/disable/'.$this->getGridIdentifier());
    }

    /**
     * get grid config value
     * @param $node
     * @return Mage_Core_Model_Config_Element
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getConfigData($node){
        return Mage::getConfig()->getNode(self::XML_ROOT_PATH.$this->getGridIdentifier().'/'.$node);
    }

    /**
     * get path to system attributes
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getXmlSystemAttributesPath(){
        return self::XML_ROOT_PATH.$this->getGridIdentifier().'/system_attributes';
    }

    /**
     * get acl path
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAclPath(){
        return (string)$this->getConfigData('acl_path');
    }

    /**
     * get entity type
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getType(){
        return (string)$this->getConfigData('type');
    }

    /**
     * get current grid settings
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getCurrentSettings(){
        return Mage::getModel('easylife_gridenhancer/settings')
            ->loadByAdmin(
                Mage::getSingleton('admin/session')->getUser()->getId(),
                $this->getGridIdentifier()
            );
    }

    /**
     * get all attributes
     * @param bool $withEmpty
     * @return mixed|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAttributes($withEmpty = false){
        if (is_null($this->_attributes)){
            $this->_attributes = array();
            if ($this->getType() == 'eav'){
                $entityTypes = $this->getEntityTypes();
                foreach ($entityTypes as $entityType=>$options){
                    if (!empty($options->collection)){
                        $labelPrefix = (string)$options->label_prefix;
                        $valuePrefix = (string)$options->prefix;
                        $collection = Mage::getResourceModel((string)$options->collection)
                            ->addVisibleFilter()
                            ->addFieldToFilter('attribute_code', array('nin'=>$this->getSystemAttributes($entityType)))
                            ->addFieldToFilter('frontend_input', array('in'=>$this->getAllowedTypes()))
                            ->setOrder('frontend_label', 'asc');
                        ;
                        foreach ($collection as $attribute){
                            $this->_attributes[] = array(
                                'label'=>$labelPrefix.$attribute->getFrontendLabel(),
                                'value'=>$valuePrefix.$attribute->getAttributeCode(),
                                'entity_type'=>$entityType
                            );
                        }
                    }
                }
            }
            elseif ($this->getType() == 'flat') {
                $allowed = (array)$this->getConfigData('allowed_attributes');
                foreach ($allowed as $code=>$options){
                    $this->_attributes[] = array(
                        'label'=>(string)$options->label,
                        'value'=>(string)$options->entity_type.'__'.$code,
                        'entity_type'=>(string)$options->entity_type
                    );
                }
            }
        }
        $attributes = $this->_attributes;
        if ($withEmpty){
            array_unshift($attributes, array('label'=>'','value'=>''));
        }
        return $attributes;
    }

    /**
     * get entity types
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getEntityTypes(){
        return (array)$this->getConfigData('entity_types');
    }

    /**
     * get entity type settings
     * @access public
     * @param $entityType
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getEntityType($entityType){
        $entityTypes = $this->getEntityTypes();
        if (isset($entityType[$entityType])){
            return (array)$entityTypes[$entityType];
        }
        return array();
    }

    /**
     * get system attributes
     * @access public
     * @param null $entityType
     * @param bool $withNames
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSystemAttributes($entityType = null, $withNames = false){
        if (is_null($this->_systemAttributes)){
            $this->_systemAttributes = array();
            $this->_allSystemAttributes = array();
            //$attributes = (array)Mage::getConfig()->getNode($this->getXmlSystemAttributesPath());
            $attributes = (array)$this->getConfigData('system_attributes');
            foreach ($attributes as $code=>$options){
                $_entityType = (string)$options->entity_type;
                if (!isset($this->_systemAttributes[$_entityType])){
                    $this->_systemAttributes[$_entityType] = array();
                }
                $this->_systemAttributes[$_entityType][$code] = (array)$options;

                $_et = $this->getEntityType($_entityType);
                if ($_et){
                    $this->_allSystemAttributes[$_et['prefix'].$code] = (array)$options;
                }
            }
        }
        if (!isset($this->_systemAttributes[$entityType])){
            $this->_systemAttributes[$entityType] = array();
        }
        if (is_null($entityType)){
            $workWith = $this->_allSystemAttributes;
        }
        else{
            $workWith = $this->_systemAttributes[$entityType];
        }
        if (!$withNames){
            return array_keys($workWith);
        }
        return $workWith;
    }
    /**
     * get allowed types
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getAllowedTypes(){
        $types = $this->getConfigData('allowed_types');
        if (empty($types)){
            return false;
        }
        return array_keys((array)$types);
    }

    /**
     * get the form save action
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSaveActionUrl(){
        return Mage::helper('adminhtml')->getUrl('adminhtml/gridenhancer_index/save', array('mode'=>$this->getGridIdentifier()));
    }

    /**
     * get header text
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getHeaderText(){
        return (string)$this->getConfigData('form_header');
    }

    /**
     * get path to menu for selection
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getMenuPath(){
        return (string)$this->getConfigData('menu_path');
    }

    /**
     * get the message for the grid
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getMessage(){
        return (string)$this->getConfigData('message');
    }

    /**
     * validate request
     * @access public
     * @return bool
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function validateRequest(){
        $requestData = $this->getConfigData('request');
        if (empty($requestData)){
            return true;
        }
        return ($this->_validatePart($requestData->module, 'getModuleName')
            && $this->_validatePart($requestData->controller, 'getControllerName')
            && $this->_validatePart($requestData->action, 'getActionName')
        );
    }

    /**
     * validate each part of the request
     * @access public
     * @param $value
     * @param $method
     * @return bool
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _validatePart($value, $method){
        if (empty($value)){
            return true;
        }
        $values = explode('|', $value);
        if (in_array(self::WILDCARD, $values)){
            return true;
        }
        $request = $this->_getRequest();
        $match = $request->$method();
        return in_array($match, $values);
    }
}