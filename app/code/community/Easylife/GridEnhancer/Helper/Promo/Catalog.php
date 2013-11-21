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
 * promo catalog helper
 *
 * @category    Easylife
 * @package     Easylife_GridEnhancer
 * @author      Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_GridEnhancer_Helper_Promo_Catalog
    extends Easylife_GridEnhancer_Helper_Abstract {
    /**
     * grid identifier
     */
    const PROMO_CATALOG_GRID_IDENTIFIER = 'promo_catalog';
    /**
     * implementation of the abstract method
     * @access public
     * @return mixed|string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getGridIdentifier(){
        return self::PROMO_CATALOG_GRID_IDENTIFIER;
    }
    /**
     * get config path to helper
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public static function getHelperPath(){
        return Easylife_GridEnhancer_Helper_Abstract::XML_ROOT_PATH.'promo_catalog/helper';
    }

}