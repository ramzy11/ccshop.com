<?php
/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Cms
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */


/**
 * CMS Hierarchy Navigation Menu source model for Display list mode
 *
 * @category   Gri
 * @package    Gri_Cms
 */
class Gri_Cms_Model_Source_Hierarchy_Menu_Listmode
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            ''          => Mage::helper('gri_cms')->__('Default'),
            '1'         => Mage::helper('gri_cms')->__('Numbers (1, 2, 3, ...)'),
            'a'         => Mage::helper('gri_cms')->__('Lower Alpha (a, b, c, ...)'),
            'A'         => Mage::helper('gri_cms')->__('Upper Alpha (A, B, C, ...)'),
            'i'         => Mage::helper('gri_cms')->__('Lower Roman (i, ii, iii, ...)'),
            'I'         => Mage::helper('gri_cms')->__('Upper Roman (I, II, III, ...)'),
            'circle'    => Mage::helper('gri_cms')->__('Circle'),
            'disc'      => Mage::helper('gri_cms')->__('Disc'),
            'square'    => Mage::helper('gri_cms')->__('Square'),
        );
    }
}
