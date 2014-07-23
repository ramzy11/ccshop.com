OriginalProductConfig = Product.Config;
JSON || (JSON = {});
JSON.toArray || (JSON.toArray = function (obj) {
    var array = [], p;
    for (p in obj) {
        array.push(obj[p]);
    }
    return array;
});
JSON.length || (JSON.length = function (obj) {
    return JSON.toArray(obj).length;
});
Product.Config = Class.create(OriginalProductConfig, {
    initialize:function (config) {
        window.flashSaleConfig = window.flashSaleConfig || null;
        if (flashSaleConfig) {
            flashSaleConfig.attributes = {};
            jQuery.each(config.attributes, function (attributeId, attribute) {
                var options = [];
                jQuery.each(attribute.options, function (optionId, option) {
                    var products = jQuery.map(option.products, function (productId) {
                        if (flashSaleConfig.availableProducts[productId]) {
                            var optionId = option.id;
                            if (!flashSaleConfig.attributes[attributeId])
                                flashSaleConfig.attributes[attributeId] = {options:{}};
                            if (!flashSaleConfig.attributes[attributeId].options[optionId])
                                flashSaleConfig.attributes[attributeId].options[optionId] = {};
                            flashSaleConfig.attributes[attributeId].options[optionId][productId] =
                                flashSaleConfig.availableProducts[productId];
                            return productId;
                        }
                        else return null;
                    });
                    if (flashSaleConfig.removeUnavailableProducts && products.length) {
                        option.products = products;
                        options.push(option);
                    }
                });
                if (flashSaleConfig.removeUnavailableProducts) config.attributes[attributeId].options = options;
            });
            config.flashSaleConfig = flashSaleConfig;
        }
        OriginalProductConfig.prototype.initialize.apply(this, arguments);
    },
    reloadPrice:function () {
        if (this.config.disablePriceReload) {
            return;
        }
        if (!this.config.flashSaleConfig || !JSON.length(flashSaleConfig.availableProducts)) {
            return OriginalProductConfig.prototype.reloadPrice.apply(this, arguments);
        }
        var price = 0;
        var oldPrice = 0;
        var products, productId;
        var hasOption = true;
        for (var i = this.settings.length - 1; i >= 0; i--) {
            var attributeId = this.settings[i].attributeId;
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            var optionId = selected.value;
            hasOption = hasOption && optionId;
            if (optionId) {
                if (flashSaleConfig.attributes[attributeId] &&
                    flashSaleConfig.attributes[attributeId].options[optionId]) {
                    products = products || Object.clone(flashSaleConfig.attributes[attributeId].options[optionId]);
                    for (productId in products) {
                        if (!flashSaleConfig.attributes[attributeId].options[optionId][productId]) delete products[productId];
                    }
                }
                else products = {};
            }
            if (selected.config) oldPrice += parseFloat(selected.config.oldPrice);
        }

        if (flashSaleConfig.availableProducts.length && !hasOption) {
            products = {"0": JSON.toArray(flashSaleConfig.availableProducts).max()};
        }

        if (products && JSON.length(products)) {
            var oldPriceContainer = $(this.config.optionsPrice.containers[4]), priceContainer = $(this.config.optionsPrice.containers[0]);
            oldPriceContainer = oldPriceContainer ? oldPriceContainer.parentNode : undefined;
            priceContainer = priceContainer ? priceContainer.parentNode : undefined;
            price = JSON.toArray(products).max() - this.config.basePrice;
            if (oldPriceContainer && priceContainer && priceContainer.hasClassName("special-price")) {
                if (price || (this.config.basePrice * 1 < this.config.oldPrice * 1)) {
                    priceContainer.show();
                    priceContainer.hasClassName("old-price") || oldPriceContainer.addClassName("old-price");
                } else {
                    priceContainer.hide();
                    oldPriceContainer.removeClassName("old-price");
                }
            }
            this.config.optionsPrice.changePrice('config', {'price':price, 'oldPrice':oldPrice});
            this.config.optionsPrice.reload();
        }
        else return OriginalProductConfig.prototype.reloadPrice.apply(this, arguments);

        return price;
    }
});
