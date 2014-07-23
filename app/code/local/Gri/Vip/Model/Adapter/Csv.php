<?php

class Gri_Vip_Model_Adapter_Csv extends Mage_ImportExport_Model_Import_Adapter_Csv
{
    protected $_hasEof;

    protected function _init()
    {
        parent::_init();
        fseek($this->_fileHandler, -6, SEEK_END);
        $eofSign = strtolower(trim(fread($this->_fileHandler, 6)));
        $this->_hasEof = $eofSign == 'eof';
        $this->rewind();
        return $this;
    }

    public function hasEof()
    {
        return $this->_hasEof;
    }
}
