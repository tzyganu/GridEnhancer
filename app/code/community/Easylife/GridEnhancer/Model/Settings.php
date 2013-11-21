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
 * Settings  model
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */

class Easylife_GridEnhancer_Model_Settings extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'gridenhancer_settings';
    /**
     * cache tag
     */
    const CACHE_TAG = 'gridenhancer_settings';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'gridenhancer_settings';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'settings';

    /**
     * constructor
     * @access protected
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('easylife_gridenhancer/settings');
    }
    /**
     * before save module
     * @access protected
     * @return Easylife_GridEnhancer_Model_Settings
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _beforeSave() {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get settings by admin id
     * @access public
     * @param $adminId
     * @param $gridCode
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function loadByAdmin($adminId, $gridCode){
        $collection = $this->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('grid_code', $gridCode);
        return $collection->getFirstItem();
    }

    /**
     * get decoded configuration
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getConfiguration(){
        return Mage::helper('core')->jsonDecode($this->getData('configuration'));
    }
}