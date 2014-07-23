<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Gri_Preorder
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Gri
 * @package    Gri_Preorder
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Gri_Preorder_AccountController extends Mage_Core_Controller_Front_Action
{

    function ajaxLoginAction()
    {
        $message = '';
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        if (!Mage::helper('gri_preorder')->isEnabled()) {
            $message = $this->__('Ajax login is disabled');
        } else {
            $username = $this->getRequest()->getParam('username', false);
            $password = $this->getRequest()->getParam('password', false);
            if (!$username || !$password) {
                $message = $this->__('Login and password are required.');
            }
            else {
                try {
                    $session->login($username, $password);
                    $this->getResponse()->setBody('<script type="text/javascript">
                    document.domain = "' . Mage::getStoreConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN) . '";
                    window.parent.location.reload();
                    </script>');
                }
                catch (Exception $e) {
                    Mage::logException($e);
                    $message = $e->getMessage();
                }
            }
        }
        $message and $session->addError($message);
        return;
    }
}
