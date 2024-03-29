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
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */

/**
 * Column renderer for messages in reward history grid
 *
 */
class Gri_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid_Column_Renderer_Reason
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render "Expired / not expired" reward "Reason" field
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _getValue(Varien_Object $row)
    {
        $expired = '';
        if ($row->getData('is_duplicate_of') !== null) {
             $expired = '<em>' . Mage::helper('gri_reward')->__('Expired reward.') . '</em> ';
        }
        return $expired . (parent::_getValue($row));
    }
}
