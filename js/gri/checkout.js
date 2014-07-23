(function($) {
    if (0) {
        window.billing = new Billing;
        window.checkout = new Checkout;
        window.billingForm = new VarienForm;
    }
    var billingNextStep = Billing.prototype.nextStep;
    Object.extend(Billing.prototype, {
        saveAddressUrl: '',

        editAddress: function(addressId, isNew) {
            var country = $('#billing\\\:country_id'),
                region = $('#billing\\\:region_id'),
                city = $('#billing\\\:city_id');
            if (isNew) {
                $('#co-billing-form :input:visible').val('');
                country.val(country.find("option[value!='']:first").val()).change().selectBox('refresh');
            }
            if ($('#billing-address-select').length) {
                $('#billing-address-select').parent().hide();
                $('#billing-address-select').selectBox('value', addressId);
            }
            $('#billing-new-address-form').show();
            $('#billing\\\:edit_mode').val(isNew ? 0 : 1);
            var countryId = country.val(),
                regionId = region.val() || region.attr('defaultValue'),
                cityId = city.val() || city.attr('defaultValue');
            country.selectBox('refresh').selectBox('value', countryId).change();
            region.selectBox('refresh').selectBox('value', regionId).change();
            city.selectBox('refresh').selectBox('value', cityId).change();
            checkout.gotoSection('billing');
        },

        nextStep: function(transport) {
            var result = billingNextStep(transport);
            $('#checkout-step-shipping_method .address-ajax-loader').hide();
            return result;
        },

        saveAddressId: function(id) {
            if (checkout.loadWaiting != false) return;
            checkout.setLoadWaiting('billing');
            var request = new Ajax.Request(
                this.saveAddressUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: 'billing_address_id=' + id
                }
            );
        },

        fillForm: function(transport) {
            var elementValues = {};
            if (transport && transport.responseText){
                try{
                    elementValues = eval('(' + transport.responseText + ')');
                }
                catch (e) {
                    elementValues = {};
                }
            }
            else{
                this.resetSelectedAddress();
            }
            var arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var fieldName = arrElements[elemIndex].id.replace(/^billing:/, '');
                    arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                    arrElements[elemIndex].setAttribute('defaultValue', elementValues[fieldName] ? elementValues[fieldName] : '');
                    if (fieldName == 'country_id' && billingForm){
                        billingForm.elementChildLoad(arrElements[elemIndex]);
                    }
                }
            }
        }
    });

    window.initBilling = function() {
        $('dl.toggle dt a').click(function() {
            var toggle = $(this), show = !toggle.hasClass('active');
            toggle.parent().parent().find('.toggle-status').val(show ? 1 : 0);
            if (show) toggle.addClass('active');
            else toggle.removeClass('active');
            toggle.parent().next('dd').toggle(show);
        });
        if ($('#fapiao').val() != '') $('#fapiao').parent().parent().find('dt a').click();
    };

    window.initShipping = function() {
        var addressSelect = $('#billing-address-select-shipping_method');
        addressSelect.change(function (e) {
            var id = addressSelect.val();
            $('#checkout-step-shipping_method .address-ajax-loader').show();
            if (id == "") {
                billing.editAddress('', true);billing.editAddress('', true);billing.editAddress('', true);
                return billing.editAddress('');
            }
            billing.setAddress(id);
            $('#billing-address-select').val(id);
            billing.saveAddressId(id);
        });
        addressSelect.selectBox();
        $('#billing-address-select').selectBox('options', (function() {
            var options = {};
            addressSelect.children().each(function(i, obj) {
                options[obj.value] = obj.text;
            });
            return options;
        })()).selectBox('value', addressSelect.val());
        $('#checkout-shipping-method-load dl.toggle dt a').click(function() {
            var toggle = $(this), show = !toggle.hasClass('active');
            toggle.parent().parent().find('.toggle-status').val(show ? 1 : 0);
            if (show) toggle.addClass('active');
            else toggle.removeClass('active');
            toggle.parent().next('dd').toggle(show);
        });
        if ($('#available-fapiao').val() != '' && !$('#available-fapiao').parent().parent().find('dt a').hasClass('active'))
            $('#available-fapiao').parent().parent().find('dt a').click();
    };
})(jQuery);
