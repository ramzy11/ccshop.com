<?php
$profile = Mage::getModel('dataflow/profile');
$name = 'Gri-Update Products';

$actions_xml = "
<action type=\"dataflow/convert_adapter_io\" method=\"load\">
    <var name=\"type\">file</var>
    <var name=\"path\">var/import</var>
    <var name=\"filename\"><![CDATA[import_products.xml]]></var>
    <var name=\"format\"><![CDATA[xml]]></var>
</action>
<action type=\"dataflow/convert_parser_xml_excel\" method=\"parse\">
    <var name=\"single_sheet\"><![CDATA[]]></var>
    <var name=\"fieldnames\"></var>
    <var name=\"mode\">update</var>
    <var name=\"store\"><![CDATA[0]]></var>
    <var name=\"number_of_records\">2</var>
    <var name=\"decimal_separator\"><![CDATA[.]]></var>
    <var name=\"adapter\">gri_importdata/convert_adapter_Product</var>
    <var name=\"method\">parse</var>
</action>
";

$profile->setData('name', $name)->setData('actions_xml', $actions_xml)->save();
