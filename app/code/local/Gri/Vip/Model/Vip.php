<?php

/**
 * @method  Gri_Vip_Model_Vip setCustomer(Mage_Customer_Model_Customer $customer) Set customer instance
 */
class Gri_Vip_Model_Vip extends Varien_Object
{
    const YEAR = '12';  // 12 months
    const SEASON = '3'; // 3 months
    const DAY = '1'; // 1 day
    const TIME_RULE = 'y-MM-dd H:m:s'; //2012-01-01 01:01:01
    const DAY_START_RULE = 'y-MM-dd 00:00:00'; //2012-01-01 01:01:01
    const DAY_END_RULE = 'y-MM-dd 23:59:59'; //2012-01-01 01:01:01
    const YMMDD_RULE = 'yMMdd';
    const PATTERN_CELLPHONE = '/^1\d{10}$/';

    /**
     *  check cellphone
     *  @param  string  $cellphone
     *
     *  @return  int
     */
    public function isCellphone($cellphone)
    {
        return preg_match(self::PATTERN_CELLPHONE, $cellphone);
    }

    /**
     *   get  total purchase in  recent time
     *
     * @param int $days
     * @param int $customerId
     *
     * @return decimal
     */
    function _getLatestTotalPurchase($days, $customerId)
    {
        $from = $this->getToday()->subDay($days)->toString(self::TIME_RULE);
        $to = $this->getToday()->toString(self::TIME_RULE);

        //  order collection
        $orders = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter('customer_id', array('eq' => $customerId))
                ->addAttributeToFilter('state', array('eq' => 'complete'))
                ->addAttributeToFilter('updated_at', array('from' => $from, 'to' => $to))
                ->load();

        // total  purchase  in  24h
        $purchase = 0;
        foreach ($orders as $order) {
            $purchase += $order->getGrandTotal();
        }

        // output
        return $purchase;
    }

    /**
     * Update customer group (upgrade or downgrade)
     *
     * @param $customer Mage_Customer_Model_Customer|integer
     * @param $toGroupId integer
     */
    protected function _updateCustomerGroup($customer, $toGroupId)
    {
        $customer instanceof Mage_Customer_Model_Customer or
            $customer = Mage::getSingleton('customer/customer')->unsetData()->load($customer);
        if ($customer->getGroupId() == $toGroupId) return;
        $fromGroupId = $customer->getGroupId();
        $customer->setGroupId($toGroupId);
        $customer->save();
        Mage::log('Upgraded/downgraded customer "' . $customer->getEmail() . '" from ' . $fromGroupId . ' to ' . $toGroupId,
            NULL, 'vip.up-downgrade.log');
    }

    /**
     * Upgrade VIP level
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return bool|integer
     */
    public function upgradeCustomerGroup(Mage_Customer_Model_Customer $customer)
    {
        $this->setCustomer($customer);
        /* @var $onlineVip Gri_Vip_Model_Relation_Online */
        $onlineVip = Mage::getSingleton('gri_vip/relation_online')->load($customer->getId(), 'customer_id');
        $offlineVip = Mage::getSingleton('gri_vip/relation_offline')->load($customer->getId(), 'customer_id');
        $customerGroupId = $customer->getGroupId();

        // Fetch group IDs for each VIP level
        $groupIdGeneral = $this->_getGroupIdByVipLevel('general');
        $groupIdOfflineVip = $this->_getGroupIdByVipLevel('offlinevip');
        $groupIdSilver = $this->_getGroupIdByVipLevel('silver');
        $groupIdGold = $this->_getGroupIdByVipLevel('gold');

        // Reset customer group id if online VIP record does not exist
        $onlineVip->getId() or $customerGroupId = $offlineVip->getId() ? $groupIdOfflineVip : $groupIdGeneral;

        // Skip customer who is already Gold VIP
        if ($customerGroupId == $groupIdGold) return;

        // Fetch purchase records
        $dailyPurchase = $this->_getLatestTotalPurchase(self::DAY, $customer->getId());
        $quarterPoints = $this->_getRecentRewardPoints(self::SEASON, $customer->getId());
        $annualPoints = $this->_getRecentRewardPoints(self::YEAR, $customer->getId());

        // Fetch upgrade conditions
        $upgradeConditionSilver = $this->_getConfig('silver');
        $upgradeConditionGold = $this->_getConfig('gold');

        // Check if can upgrade to Gold
        if ($dailyPurchase >= $upgradeConditionGold['entry_requirement'] ||
            $annualPoints >= $upgradeConditionGold['upgrade_requirement']
        ) {
            $customerGroupId = $groupIdGold;
        }
        // Check if can upgrade to Silver
        else if (
            $dailyPurchase >= $upgradeConditionSilver['entry_requirement'] ||
            $quarterPoints >= $upgradeConditionSilver['upgrade_requirement']
        ) {
            $customerGroupId = $groupIdSilver;
        }

        // Do upgrade
        if ($customerGroupId != $customer->getGroupId()) {
            // Update online VIP record (Silver and Gold)
            in_array($customerGroupId, array($groupIdSilver, $groupIdGold)) and $this->updateOnlineVip($customer);
            // Update customer group
            $this->_updateCustomerGroup($customer, $customerGroupId);
        }
        return $customerGroupId;
    }

    /**
     * Get customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->getData('customer') === NULL) {
            $this->setData('customer', Mage::getSingleton('customer/session')->getCustomer());
        }
        return $this->getData('customer');
    }

    /**
     * @return Gri_Vip_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('gri_vip');
    }

    /**
     * @return Zend_Date
     */
    public function getToday()
    {
        return Mage::getSingleton('core/locale')->date();
    }

    /**
     * Get group id by VIP level
     * @param string $level
     * @return integer
     */
    protected function _getGroupIdByVipLevel($level)
    {
        return $this->getHelper()->getGroupIdByVipLevel($level);
    }

    /**
     * Get VIP classification requirement
     *
     * @param string $level
     * @return array
     */
    protected function _getConfig($level)
    {
        return $this->getHelper()->getLevelRequirement($level);
    }

    /**
     *   during  recent time ,customer get reward  points
     *
     *   @param  int  $time
     *   @param  int  $customerId
     *
     *   @return  int
     */
    public function _getRecentRewardPoints($time, $customerId) {
        $now = $this->getToday()->toString(self::TIME_RULE);

        // year
        if ($time == 12) {
            $from = $this->getToday()->subYear(1)->toString(self::TIME_RULE);
        } elseif ($time == 3) { // season
            $from = $this->getToday()->subMonth(3)->toString(self::TIME_RULE);
        }

        $sql = "select `b`.`points_delta` from `gri_reward` a  inner  join `gri_reward_history`  b  on `a`.`reward_id`=`b`.`reward_id`  where `b`.`created_at`>='" .
                $from . "' and  `b`.`created_at`<='" . $now . "'  and `a`.`customer_id`='" . $customerId . "'";
        $w = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $w->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        $total_points = 0;
        foreach ($rows as $r) {
            if ($r['points_delta'] > 0) {
                $total_points += $r['points_delta'];
            }
        }

        //output
        return $total_points;
    }

    public function scheduledAnnualRenewal()
    {
        // Get online VIPs to be renewed
        $to = $this->getToday()->toString(self::DAY_END_RULE);
        $nextRenewalTime = $this->getToday()->addYear(1)->toString(self::TIME_RULE);
        $collection = Mage::getSingleton('gri_vip/relation_online')->getCollection()
            ->addFieldToFilter('state', 1)
            ->addFieldToFilter('annual_time', array('to' => $to));

        /* @var $onlineVip Gri_Vip_Model_Relation_Online */
        foreach ($collection as $onlineVip) {
            $customerId = $onlineVip->getCustomerId();
            /* @var $customer Gri_Customer_Model_Customer */
            $customer = Mage::getSingleton('customer/customer')->unsetData()->load($customerId);

            // Fetch group IDs for each VIP level
            $groupIdGeneral = $this->_getGroupIdByVipLevel('general');
            $groupIdOfflineVip = $this->_getGroupIdByVipLevel('offlinevip');
            $groupIdSilver = $this->_getGroupIdByVipLevel('silver');
            $groupIdGold = $this->_getGroupIdByVipLevel('gold');

            // Skip non-online VIP customers
            if (!in_array($groupId = $customer->getGroupId(), array($groupIdSilver, $groupIdGold))) {
                $this->setVipState($onlineVip, 0);
                continue;
            }

            // Fetch renewal conditions
            $renewalConditionSilver = $this->_getConfig('silver');
            $renewalConditionGold = $this->_getConfig('gold');
            $points = $this->_getRecentRewardPoints(self::YEAR, $customerId);

            // Check if can downgrade to non-online VIP
            if ($points < $renewalConditionSilver['annual_renewal_requirement']) {
                /* @var $offlineVip Gri_Vip_Model_Relation_Offline */
                $offlineVip = Mage::getSingleton('gri_vip/relation_offline')->unsetData()->load($customer->getId(), 'customer_id');
                // Downgrade to offline VIP if applicable
                $groupId = $offlineVip->getId() ? $groupIdOfflineVip : $groupIdGeneral;
            }
            // Check if can downgrade to Silver VIP
            else if ($points < $renewalConditionGold['annual_renewal_requirement']) {
                $groupId = $groupIdSilver;
            }

            $this->_updateCustomerGroup($customer, $groupId);
            // Update annual renew  at the time  next year
            if (in_array($groupId, array($groupIdSilver, $groupIdGold))) {
                $this->_updateOnlineAnnualTime($onlineVip, $nextRenewalTime);
            }
            // Set VIP state to 0 if no longer be online VIP
            else {
                $this->setVipState($onlineVip, 0);
            }
        }
    }

    /**
     * @param Gri_Vip_Model_Relation_Online $onlineVip
     * @param integer $state
     * @return Gri_Vip_Model_Vip
     */
    public function setVipState($onlineVip, $state)
    {
        $onlineVip->getId() and $onlineVip->setState($state)->save();
        return $this;
    }

    /**
     * Update annual renewal time
     *
     * @param Gri_Vip_Model_Relation_Online $onlineVip
     * @param $nextRenewalTime
     * @return Gri_Vip_Model_Vip
     */
    protected function _updateOnlineAnnualTime($onlineVip, $nextRenewalTime)
    {
        $onlineVip->setAnnualTime($nextRenewalTime)->save();
        return $this;
    }

    /**
     * Generate VIP no for online VIP
     *
     * @return string
     */
    protected function _generateOnlineVipNo()
    {
        return md5(microtime());
    }

    /**
     * Update / create online VIPs
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Gri_Vip_Model_Relation_Online
     */
    public function updateOnlineVip(Mage_Customer_Model_Customer $customer)
    {
        /* @var $onlineVip Gri_Vip_Model_Relation_Online */
        $onlineVip = Mage::getSingleton('gri_vip/relation_online')->unsetData()->load($customer->getId(), 'customer_id');

        $now = $this->getToday()->toString(self::TIME_RULE);
        $annualTime = $this->getToday()->addYear(1)->toString(self::TIME_RULE);

        // Create new record if not exists
        if (!$onlineVip->getId()) {
            $onlineVip->setData(array(
                'customer_id' => $customer->getId(),
                'create_time' => $now,
                'card_no' => $this->_generateOnlineVipNo(),
            ));
        }
        $onlineVip->setUpdateTime($now);
        $this->_updateOnlineAnnualTime($onlineVip, $annualTime);
        return $onlineVip;
    }

    /**
     * Import csv-data to gri_vip_relation_offline
     */
    public function createOfflineVipRec()
    {
        $timestamp = time();

        $io = $this->getFileIo();
        $ftpPath = Mage::getBaseDir('var') . DS . 'ftp' . DS . 'vip' . DS;
        $csvPath = $ftpPath . 'from_offline';
        is_dir($csvPath) or $io->mkdir($csvPath);

        //check history path
        $historyPath = $ftpPath . 'history';
        is_dir($historyPath) or $io->mkdir($historyPath);

        $csvFile = $csvPath . DS . 'import_offlinevip.csv';
        if (is_file($csvFile)) {
            $historyFile = $historyPath . DS . basename($csvFile, '.csv') . '-' . date('Y-m-d-H-i', $timestamp) . '.csv';
            //load csv file
            /* @var $csvAdapter Gri_Vip_Model_Adapter_Csv */
            $csvAdapter = Mage::getModel('gri_vip/adapter_csv', $csvFile);
            // Integrity checking
            if ($csvAdapter->hasEof()) {
                /* @var $offlineVip Gri_Vip_Model_Relation_Offline */
                $offlineVip = Mage::getModel('gri_vip/relation_offline');
                $offlineVip->getResource()->resetState();
                while ($csvAdapter->valid()) {
                    //get current row point
                    $data = $csvAdapter->current();
                    // Skip EOF sign
                    if (strtolower(trim(reset($data))) == 'eof') break;
                    foreach ($data as $k => $v) {
                        $k = trim($k);
                        $v = trim($v);
                        $data[$k] = $v;
                    }

                    $cardNo = $data['vip_id'];
                    $mobile = $data['mobile'];

                    $data = array(
                        'card_no' => $cardNo,
                        'mobilephone' => $mobile,
                        'state' => 1,
                    );

                    $message = '';
                    if ($this->isCellphone($data['mobilephone'])) {
                        try {
                            $offlineVip->unsetData()->load($cardNo, 'card_no');
                            $offlineVip->addData($data);
                            $offlineVip->save();
                        } catch (Exception $e) {
                            $message = 'Failed to save';
                            $this->log($e->getMessage());
                        }
                    } else {
                        $message = "Mobilephone doesn't Match Rule";
                    }

                    //log error
                    if ($message) {
                        $data['date'] = $timestamp;
                        $data['message'] = $message;
                        $this->logError($data);
                    }

                    // to next row
                    $csvAdapter->next();
                }
                $offlineVip->getResource()->syncCustomerGroups();
            }
            else {
                $message = 'File integrity checking failure: ' . $historyFile;
                $this->log($message);
            }
            //close file handle
            $csvAdapter->__destruct();
            // move file to history folder and remove source
            rename($csvFile, $historyFile);
        }
    }

    public function logError($data)
    {
        $card_no = $data['card_no'];
        $mobile = $data['mobilephone'];
        $message = $data['message'];
        $date = $data['date'] ;
        $input_data = array();

        $targetPath = Mage::getBaseDir('var') . DS . 'ftp' . DS . 'vip' . DS . 'from_offline' . DS . 'logFile';
        if(!is_dir($targetPath)){
           @mkdir($targetPath,0777,true);
        }

        $targetPath = $targetPath . DS . 'error_'.date('Y-m-d-H-i',$date).'.csv';
        $data = array();
        if(!is_file($targetPath)){
          $input_data[] = array('vip_id', 'mobilephone','error_message');
        }

        $fp = fopen($targetPath, 'a');
        $input_data[] = array($card_no, $mobile,$message);
        foreach ($input_data as $r) {
          fputcsv($fp, $r);
        }

        fclose($fp);
    }

    /**
     *  create  bind  rec  of  vip id and   offline vip id
     *
     */
    function exportVipidAndOfflineVipIdRecFile() {

        //$csvAdapter = Mage::getSingleton('ImportExport/export_adapter_csv');
        $sql = 'select  `a`.`card_no` as  `online_vip_id` , `b`.`card_no`  as  `offline_vip_id`  from  `gri_vip_relation_online` a  inner join  `gri_vip_relation_offline` b  ON  `a`.`customer_id`= `b`.`customer_id`';
        $w = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $w->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        $date_ymmdd = $this->getToday()->toString(self::YMMDD_RULE);

        $path = Mage::getBaseDir('var') . DS . 'var' . DS . 'ftp' . DS . 'vip' . DS . 'to_offline' . DS;
        if(!is_dir($path)){
           $this->getFileIo()->mkdir($path);
        }

        $path = $path . 'vip_relation_' . $date_ymmdd . '.csv';

        $fp = fopen($path, 'w');
        $data = array();
        $data[] = array('online_vip_id', 'offline_vip_id');
        foreach ($rows as $row) {
            $data[] = array($row['online_vip_id'], $row['offline_vip_id']);
        }

        foreach ($data as $_data) {
            fputcsv($fp, $_data);
        }

        fclose($fp);
    }

    /**
     *  @return  Varien_Io_File
     *
     */
    public function getFileIo()
    {
        return new Varien_Io_File();
    }

    public function log($message)
    {
        Mage::log($message, Zend_Log::WARN, 'offlinevip.log');
    }
}
