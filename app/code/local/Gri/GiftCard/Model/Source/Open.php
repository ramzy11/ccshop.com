<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Gri_GiftCard
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Gri_GiftCard_Model_Source_Open extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        $result = array();

        foreach ($this->_getValues() as $k=>$v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }

        return $result;
    }

    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    protected function _getValues()
    {
        return array(
            Gri_GiftCard_Model_Giftcard::OPEN_AMOUNT_DISABLED => Mage::helper('gri_giftcard')->__('No'),
            Gri_GiftCard_Model_Giftcard::OPEN_AMOUNT_ENABLED  => Mage::helper('gri_giftcard')->__('Yes'),
        );
    }
    /**
     * Retrive Flat columns structure
     * @return array
     */
    public function getFlatColums()
    {
        $attributeDefaultValue = $this->getAttribute()->getDefaultValue();
        return array(
            $this->getAttribute()->getAttributeCode() => array(
                'type'      => $this->getAttribute()->getBackendType(),
                'unsigned'  => false,
                'is_null'   => is_null($attributeDefaultValue) || empty($attributeDefaultValue),
                'default'   => is_null($attributeDefaultValue) || empty($attributeDefaultValue)?null:$attributeDefaultValue,
                'extra'     => null
        ));
    }
    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
