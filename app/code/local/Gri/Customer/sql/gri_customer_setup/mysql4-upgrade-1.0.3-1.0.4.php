<?php
/* @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$config = Mage::getModel('core/config_data')->load('customer/address_templates/html' ,'path');

/*   Default Html Template:
      <div class="name">
      <div class="title">Name:</div>
        <div class="content">{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}</div>
      </div>
      <div class="phone ">
        {{depend telephone}}<div class="title">Phone:</div> <div class="content">{{var telephone}}</div>{{/depend}}
      </div>
      <div class="address">
        {{depend company}}{{var company}}<br />{{/depend}}
        <div class="title">Address: </div> <div class="content">{{if street1}}{{var street1}}<br />{{/if}}
        {{depend street2}}{{var street2}}<br />{{/depend}}
        {{depend street3}}{{var street3}}<br />{{/depend}}
        {{depend street4}}{{var street4}}<br />{{/depend}}
        {{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br/>
        {{var country}}<br/> </div>
      </div>
      {{depend fax}}<br/>F: {{var fax}}{{/depend}}
      {{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}
 *
 */

$value = '<div class="name">
            <div class="content">{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}</div>
          </div>
          <div class="address">
            {{depend company}}{{var company}}<br />{{/depend}}
          <div class="content">{{if street1}}{{var street1}}<br />{{/if}}
                {{depend street2}}{{var street2}}<br />{{/depend}}
                {{depend street3}}{{var street3}}<br />{{/depend}}
                {{depend street4}}{{var street4}}<br />{{/depend}}
                {{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br/>
                {{var country}}<br/> </div>
          </div>
          {{depend telephone}}<br/>T: {{var telephone}}{{/depend}}
          {{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}';

$config->setValue($value);
$config->save();


$installer->endSetup();
