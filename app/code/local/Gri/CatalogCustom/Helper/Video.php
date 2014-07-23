<?php

class Gri_CatalogCustom_Helper_Video extends Mage_Core_Helper_Abstract
{
    protected $_videoParams = array(
        'youku.com' => array(
            'embed',
            'type' => 'application/x-shockwave-flash',
            'src' => '',
            'width' => 400,
            'height' => 400,
            'quality' => 'high',
            'align' => 'middle',
            'allowScriptAccess' => 'sameDomain',
            'allowFullscreen' => 'true',
        ),
        'youtube.com' => array(
            'iframe',
            'src' => '',
            'width' => 400,
            'height' => 400,
            'frameborder' => '0',
            'allowFullscreen' => 'true',
        ),
        'youtu.be' => array(
            'iframe',
            'src' => '',
            'width' => 400,
            'height' => 400,
            'frameborder' => '0',
            'allowFullscreen' => 'true',
        ),
        'bokecc.com' => array(
            'embed',
            'type' => 'application/x-shockwave-flash',
            'src' => '',
            'width' => 400,
            'height' => 400,
            'allowFullscreen' => 'true',
            'allowScriptAccess' => 'always',
            'pluginspage' => 'http://www.macromedia.com/go/getflashplayer'
        ),
    );

    public function checkVideoStatus(Mage_Catalog_Model_Product $product)
    {
        return $product->getData('video_url') || $product->getData('video_url_cn');
    }

    public function getVideoHtml(Mage_Catalog_Model_Product $product)
    {
        if ($product->getVideoHtml() === NULL) {
            $html = '';
            if ($this->checkVideoStatus($product)) {
                $videoUrl = $this->getVideoUrl($product);
                $videoHost = '';
                foreach ($this->_videoParams as $k => $v) {
                    if (stripos($videoUrl, $k) !== FALSE) {
                        $videoHost = $k;
                        break;
                    }
                }
                if ($videoHost) {
                    $this->setVideoParam($videoHost, 'src', $videoUrl);
                    $tag = array_shift($this->_videoParams[$videoHost]);
                    $parmas = array();
                    foreach ($this->_videoParams[$videoHost] as $key => $value) {
                        $parmas[] = $key . '="' . $value . '"';
                    }
                    $parmas = implode(' ', $parmas);
                    $html = '<' . $tag . ' ' . $parmas . '></' . $tag . '>';
                }
            }
            $product->setVideoHtml($html);
        }
        return $product->getVideoHtml();
    }

    public function getVideoUrl(Mage_Catalog_Model_Product $product)
    {
        $ipCountry = Mage::getSingleton('gri_core/ip')
            ->ipToCountry(Mage::app()->getRequest()->getClientIp());
        $url = $ipCountry == 'CN' ? $product->getVideoUrlCn() : $product->getVideoUrl();
        $url or $url = $product->getVideoUrl();
        return $url;
    }

    public function setVideoParam($host, $key, $value = NULL)
    {
        if ($value === NULL) unset($this->_videoParams[$host][$key]);
        else $this->_videoParams[$host][$key] = $value;
        return $this;
    }
}
