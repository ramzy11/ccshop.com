<?php

class Gri_Core_Model_Email_Template extends Mage_Core_Model_Email_Template
{

    public function getLogoAlt($store)
    {
        return $this->_getLogoAlt($store);
    }

    public function getLogoUrl($store)
    {
        return $this->_getLogoUrl($store);
    }

    public function send($email, $name = NULL, array $variables = array())
    {
        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return FALSE;
        }

        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = NULL;
                break;
        }

        if ($returnPathEmail !== NULL) {
            $mailTransport = new Zend_Mail_Transport_Sendmail("-f" . $returnPathEmail);
            Zend_Mail::setDefaultTransport($mailTransport);
        }

        foreach ($emails as $key => $email) {
            $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
        }

        $this->setUseAbsoluteLinks(TRUE);
        $text = $this->getProcessedTemplate($variables, TRUE);

        if ($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject('=?utf-8?B?' . base64_encode($subject = $this->getProcessedTemplateSubject($variables)) . '?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());

        if (Mage::getIsDeveloperMode()) {
            Mage::log('Email to: ' . implode(', ', $names) . "\nSubject: " . $subject . "\nBody:\n" . $text, Zend_Log::DEBUG, 'mail.log.html');
        }

        try {
            $mail->send();
            $this->_mail = NULL;
        } catch (Exception $e) {
            $this->_mail = NULL;
            Mage::getIsDeveloperMode() or Mage::logException($e);
            return FALSE;
        }

        return TRUE;
    }

    public function sendTransactional($templateId, $sender, $email, $name, $vars = array(), $storeId = NULL)
    {
        foreach ($vars as $v) {
            if ($v instanceof Varien_Object) {
                $vStoreId = (int)$v->getStoreId();
                $vStore = $vStoreId ? $vStoreId : $v->getStore();
                $vStoreId or $vStoreId  = (int)($vStore instanceof Mage_Core_Model_Store ? $vStore->getId() : $vStore);
                if ($vStoreId) {
                    $storeId = $vStoreId;
                    $this->emulateDesign($storeId);
                    break;
                }
            }
        }
        parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
        $this->revertDesign();
        return $this;
    }
}
