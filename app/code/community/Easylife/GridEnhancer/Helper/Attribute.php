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
 * attribute helper
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Helper_Attribute
    extends Easylife_GridEnhancer_Helper_Abstract {
    /**
     * grid identifier
     */
    const ATTRIBUTE_GRID_IDENTIFIER = 'catalog_product_attribute';

    /**
     * implementation of the abstract method
     * @access public
     * @return mixed|string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridIdentifier(){
        return self::ATTRIBUTE_GRID_IDENTIFIER;
    }

    /**
     * get config path to helper
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public static function getHelperPath(){
        return Easylife_GridEnhancer_Helper_Abstract::XML_ROOT_PATH.'catalog_product_attribute/helper';
    }
}