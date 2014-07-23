<?php
/**
 *
 * @author alanstorm@alanstorm.com
 * @link http://alanstorm.com/layouts_blocks_and_templates
 *
 * @author fishdrowned@gmail.com
 * @version 1.1.0 Added displayblock
 *
 */

class Fishdrowned_Layoutviewer_Model_Observer extends Varien_Object
{

    const FLAG_SHOW_LAYOUT = 'showLayout';

    const FLAG_SHOW_LAYOUT_FORMAT = 'showLayoutFormat';

    const HTTP_HEADER_TEXT = 'Content-Type: text/plain';

    const HTTP_HEADER_HTML = 'Content-Type: text/html';

    const HTTP_HEADER_XML = 'Content-Type: text/xml';

    private $request;

    private function init()
    {
        $this->setLayout(Mage::app()->getFrontController()
            ->getAction()
            ->getLayout());
        $this->setUpdate($this->getLayout()
            ->getUpdate());
    }

    public function checkForBlockDisplayRequest($observer)
    {
        if (!isset($_GET['displayblock']) && !is_file(Mage::getBaseDir() . DS . 'displayblock')) return;
        /* @var $block Mage_Core_Block_Template */
        $block = $observer->getBlock();
        $transportObject = $observer->getTransport();
        $additionalInfo = array();
        $block instanceof Mage_Cms_Block_Block and $additionalInfo[] = 'id=' . $block->getBlockId();
        $html = $transportObject->getHtml();
        $docType = '';
        if (strtolower(substr($html, 0, 14)) == '<!doctype html' && FALSE !== $pos = strpos($html, '>')) {
            $docType = substr($html, 0, $pos + 1);
            $html = substr($html, $pos + 1);
        }
        $tag = isset($_GET['explicit']) ? array('<pre>', '</pre>') : array('<!-- ', ' -->');
        $transportObject->setHtml($docType . $tag[0] . $block->getType() . ', ' . get_class($block) . ' ' . implode(', ', $additionalInfo)
            . ', ' . $block->getNameInLayout() . ', ' . $block->getTemplate() . $tag[1] . $html
            . $tag[0] . 'end of block ' . $block->getNameInLayout() . $tag[1]);
    }

    // entry point
    public function checkForLayoutDisplayRequest($observer)
    {
        $this->init();
        $is_set = array_key_exists(self::FLAG_SHOW_LAYOUT, $_GET);
        if ($is_set && 'package' == $_GET[self::FLAG_SHOW_LAYOUT]) {
            $this->outputPackageLayout();
        }
        else if ($is_set && 'page' == $_GET[self::FLAG_SHOW_LAYOUT]) {
            $this->outputPageLayout();
        }
        else if ($is_set && 'handles' == $_GET[self::FLAG_SHOW_LAYOUT]) {
            $this->outputHandles();
        }
    }

    private function outputHandles()
    {
        $update = $this->getUpdate();
        $handles = $update->getHandles();
        echo '<h1>', 'Handles For This Request', '</h1>' . "\n";
        echo '<ol>' . "\n";
        foreach ($handles as $handle) {
            echo '<li>', $handle, '</li>';
        }
        echo '</ol>' . "\n";
        die();
    }

    private function outputHeaders()
    {
        $is_set = array_key_exists(self::FLAG_SHOW_LAYOUT_FORMAT, $_GET);
        $header = self::HTTP_HEADER_XML;
        if ($is_set && 'text' == $_GET[self::FLAG_SHOW_LAYOUT_FORMAT]) {
            $header = self::HTTP_HEADER_TEXT;
        }
        header($header);
    }

    private function outputPageLayout()
    {
        $layout = $this->getLayout();
        $this->outputHeaders();
        die($layout->getNode()->asXML());
    }

    private function outputPackageLayout()
    {
        $update = $this->getUpdate();
        $this->outputHeaders();
        die($update->getPackageLayout()->asXML());
    }
}
