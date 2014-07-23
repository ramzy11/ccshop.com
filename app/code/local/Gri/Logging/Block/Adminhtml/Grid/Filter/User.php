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
 * @package     Gri_Logging
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */

/**
 * User column filter for Event Log grid
 */
class Gri_Logging_Block_Adminhtml_Grid_Filter_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * Build filter options list
     *
     * @return array
     */
    public function _getOptions()
    {
        $options = array(array('value' => '', 'label' => Mage::helper('gri_logging')->__('All Users')));
        foreach (Mage::getResourceModel('gri_logging/event')->getUserNames() as $username) {
            $options[] = array('value' => $username, 'label' => $username);
        }
        return $options;
    }

    /**
     * Filter condition getter
     *
     * @string
     */
    public function getCondition()
    {
        return $this->getValue();
    }
}
