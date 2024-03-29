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
 * @package     Gri_GiftCardAccount
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Gri_GiftCardAccount_Model_Mysql4_Giftcardaccount extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_giftcardaccount/giftcardaccount', 'giftcardaccount_id');
    }


    /**
     * Get gift card account ID by specified code
     *
     * @param string $code
     * @return mixed
     */
    public function getIdByCode($code)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), $this->getIdFieldName());
        $select->where('code = ?', $code);

        if ($id = $this->_getReadAdapter()->fetchOne($select)) {
            return $id;
        }

        return false;
    }

    /**
     * Update gift card accounts state
     *
     * @param array $ids
     * @param int $state
     * @return Gri_GiftCardAccount_Model_Mysql4_Giftcardaccount
     */
    public function updateState($ids, $state)
    {
        $bind = array('state'=>$state);
        $where = $this->_getReadAdapter()->quoteInto($this->getIdFieldName() . ' IN (?)', $ids);

        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);
        return $this;
    }
}
