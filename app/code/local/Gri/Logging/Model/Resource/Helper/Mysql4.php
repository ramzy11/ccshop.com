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
 * Eav Mysql4 resource helper model
 *
 * @category    Gri
 * @package     Gri_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Logging_Model_Resource_Helper_Mysql4 extends Mage_Eav_Model_Resource_Helper_Mysql4
{
    /**
     * Returns expression for converting int field to IP string
     *
     * @param string | null $field if field is null then this field must be user for prepareSqlCondition
     * @return Zend_Db_Expr
     */
    public function getInetNtoaExpr($field = null)
    {
        $field = $field ? $this->_getReadAdapter()->quoteIdentifier($field) : '#?';
        return new Zend_Db_Expr('INET_NTOA(' . $field . ')');
    }
}
