<?php
$profile = Mage::getModel('dataflow/profile');
$name = 'Gri-Import Products';

//<action type=\"dataflow/convert_adapter_io\" method=\"load\">
//    <var name=\"type\">file</var>
//    <var name=\"path\">var/import</var>
// <var name=\"filename\"><![CDATA[import_products.csv]]></var>
//    <var name=\"format\"><![CDATA[csv]]></var>
//</action>
//<action type=\"dataflow/convert_parser_csv\" method=\"parse\">
//    <var name=\"delimiter\"><![CDATA[,]]></var>
//    <var name=\"enclose\"><![CDATA[\"]]></var>
//    <var name=\"fieldnames\">true</var>
//    <var name=\"store\"><![CDATA[0]]></var>
//    <var name=\"number_of_records\">1</var>
//    <var name=\"decimal_separator\"><![CDATA[.]]></var>
//    <var name=\"adapter\">gri_importdata/convert_adapter_Product</var>
//    <var name=\"method\">parse</var>
//</action>
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
    <var name=\"store\"><![CDATA[0]]></var>
    <var name=\"number_of_records\">2</var>
    <var name=\"decimal_separator\"><![CDATA[.]]></var>
    <var name=\"adapter\">gri_importdata/convert_adapter_Product</var>
    <var name=\"method\">parse</var>
</action>

";

$profile->setData('name',$name)->setData('actions_xml',$actions_xml)->save();