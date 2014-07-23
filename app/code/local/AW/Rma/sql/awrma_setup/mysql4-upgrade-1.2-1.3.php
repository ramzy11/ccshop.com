<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.3.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


$installer = $this;
$installer->startSetup();



$oldToCustomer = "<p>Your RMA {{var request.getTextId()}} has been approved.</p>\r\n{{depend request.getNotifyPrintlabelAllowed()}}<p>You can print a \"Return Shipping Authorization\" label with return address and other information by pressing link above. A \"Return Shipping Authorization\" label must be enclosed inside your package; you may want to keep a copy of \"Return Shipping Authorization\" label for your records.</p>\r\n{{/depend}}\r\n<p>Please send your package to:</p>\r\n<p>{{var request.getNotifyRmaAddress()}}</p>\r\n{{depend request.getConfirmShippingIsRequired()}}<p>and press \"Confirm Sending\" button after.</p>{{/depend}}";
$newToCustomer = "<p>Your RMA {{var request.getTextId()}} has been approved.</p>\r\n{{depend request.getNotifyPrintlabelAllowed()}}<p>You can print a \"Return Shipping Authorization\" label with return address and other information by pressing link below. A \"Return Shipping Authorization\" label must be enclosed inside your package; you may want to keep a copy of \"Return Shipping Authorization\" label for your records.</p>\r\n{{/depend}}\r\n<p>Please send your package to:</p>\r\n<p>{{var request.getNotifyRmaAddress()}}</p>\r\n{{depend request.getConfirmShippingIsRequired()}}<p>and press \"Confirm Sending\" button after.</p>{{/depend}}";


$sql = "UPDATE {$this->getTable('awrma/entity_status')}
           SET `to_customer` = '{$newToCustomer}'
         WHERE `name` = 'Approved'
           AND `to_customer` = '{$oldToCustomer}'
         ;";
$installer->getConnection()->getConnection()->query($sql);




$sql = "

CREATE TABLE IF NOT EXISTS {$this->getTable('awrma/status_template')}  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,

  `name` tinytext NOT NULL,
  `to_customer` text NOT NULL,
  `to_admin` text NOT NULL,
  `to_chatbox` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table contain possible statuses of RMA requests' AUTO_INCREMENT=7 ;

";


$installer->run($sql);



$sql = "

INSERT INTO {$this->getTable('awrma/status_template')}   (`status_id`, `name`,`to_customer` , `to_admin` , `to_chatbox`)
SELECT `id`,`name` , `to_customer` , `to_admin` , `to_chatbox`
FROM {$this->getTable('awrma/entity_status')}
WHERE 1;

";

$installer->run($sql);



$installer->endSetup();
