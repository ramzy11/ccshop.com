<?php

/**
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Kai Lu <lukai.rat@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute('customer', 'weibo_uid', array(
    'type' => 'varchar',
    'label' => 'Weibo Uid',
    'visible' => false,
    'required' => false
));
$installer->endSetup();