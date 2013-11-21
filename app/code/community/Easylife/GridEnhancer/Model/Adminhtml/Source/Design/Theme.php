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
 * source model for available themes
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Model_Adminhtml_Source_Design_Theme {
    /**
     * get list as options
     * @access public
     * @param bool $withEmpty
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function toOptionArray($withEmpty = true){
        return Mage::getSingleton('core/design_source_design')->getAllOptions($withEmpty);
    }
}