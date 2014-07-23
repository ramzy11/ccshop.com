(function ($){
    $.fn.maxHeight = function () {
        return Math.max.apply(null, this.map(function () {
            return $(this).height();
        }).get());
    };
    $.fn.maxOuterHeight = function (margin) {
        return Math.max.apply(null, this.map(function () {
            var o = $(this);
            return o.outerHeight() + parseInt(o.css('margin-top')) + parseInt(o.css('margin-bottom'));
        }).get());
    };
})(jQuery);
Hamper = Class.create();
(function (_) {
    Hamper.prototype = {
        initialize: function (config) {
            this.pickIndex = 0;
            this.config = config;
            this.continueButton = _(config.continueButton || '#continue_button');
            this.pickContainer = _(config.pickContainer || '#pick_container');
            this.itemTemplate = this.pickContainer.children('li.template');
            this.discountContainer = this.pickContainer.parent().find('.discount .price');
            this.giftContainer = this.pickContainer.parent().find('.hamper-gift-message');
            this.hamperItems = _('.hamper-items li.item');
            this.minItemsAlert = _(config.minItemsAlert || '#min_items_alert');
            this.messageBox = _(config.messageBox || '#message_box');
            this.fixedBar = this.pickContainer.parent();
            this.sideBar = this.fixedBar.parent();

            // Set pre-configured values for correct price base calculation
            if (config.defaultValues) {
                this.config.selected = {};
                for (var option in config.defaultValues) {
                    var selected = [];
                    _.each(config.defaultValues[option], function (i, v) {
                        _.each(v, function (_i, _v) {
                            selected.push({id: _i, options: _v});
                        });
                    });
                    this.config.selected[option] = selected;
                }
            }
            if (config.minItems > JSON.length(config.availableSelection)) {
                this.continueButton.html('<span class="out-of-stock">' + Translator.translate('Sold out') + '</span>');
            }
            _.each(this.config.selected, function (option, ids) {
                _.each(ids, function (i, selection) {
                    var config = {};
                    if (typeof selection == "object") {
                        config.selectedOptions = selection.options;
                        selection = selection.id;
                    }
                    this.add(option, selection, config);
                }.bind(this));
            }.bind(this));
            _(document).ready(function() {
                this.addToCartForm = window['productAddToCartForm' + config.bundleId];
                this.hamperItems.each(function(i, li) {
                    li = _(li);
                    var a = li.find('.product_image a'),
                        description = li.find('.item-description'),
                        media = description.find('.item-media');
                    if (description.length) {
                        media.appendTo(description.children('.tab_box'));
                        _('<li><a href="#' + media.prop('id') + '">' + Translator.translate('Photos') + '</a></li>').prependTo(description.find('.tab-header'));
                        a.fancybox({
                            titleShow: false,
                            onStart: function () {
                                li.find('img[url]').each(function() {
                                    var img = _(this), url = img.attr('url');
                                    img.prop('src', url).removeAttr('url');
                                });
                            }
                        }).prop('href', '#' + description.prop('id'));
                    }
                });
                _('.tab_box').tabs();
            }.bind(this));
            var onScroll = function() {
                var offset = this.sideBar.offset();
                if (_(window).width() >= _('body>.wrapper').width() &&
                    _(window).height() >= this.fixedBar.height() &&
                    _(window).scrollTop() > offset.top
                ) {
                    this.fixedBar.css('position','fixed').css({'left': offset.left, 'top': 0});
                } else {
                    this.fixedBar.css('position','static');
                }
            }.bind(this);
            _(window).scroll(onScroll).resize(onScroll);
        },
        add: function (option, selection, config) {
            // Get target item to be added from hamper items list
            var item = _('ul[option=' + option + '] li[selection=' + selection + ']'),
                pickIndex = this.pickIndex,
                objThis = this,
                price = item.attr('price');
            if (item.length) {
                this.minItemsAlert.html('').hide();
                _('.hamper-wrapper .short-desc').hide();
                config = config || {};
                // Clone item to pick area
                var newItem = this.itemTemplate.clone(),
                    product = item.attr('product'),
                    targetProduct = product;
                if (this.config.isFixedPrice) this.pickContainer.children('[selection=' + selection + ']').remove();
                if (!this.config.options[option].isMulti) this.pickContainer.children('[option=' + option + ']').remove();
                newItem.removeClass('template').attr('option', option).attr('selection', selection).appendTo(this.pickContainer);
                newItem.find('.product-img img').prop('src', item.find('.product_image img').prop('src'));
                newItem.find('.product-name').html(item.find('.product-name').html());
                newItem.find('.product-price').html(this.formatPrice(price));
                newItem.find('.bundle-field').prop('name', 'bundle_option[' + option + '][' + pickIndex +
                    ']').val(selection)
                newItem.data('price', price);
                // Process configurable products
                if (item.hasClass('configurable')) {
                    var optionContainer = newItem.find('.product-option'),
                        spConfig = window['spConfig' + product],
                        fixedOptions = item.find('.options li.hover').length ?
                            item.find('.options li.hover') : item.find('.options li:first'),
                        processedOptions = {}, selectedOptions = config.selectedOptions || {},
                        allowedProducts = JSON.toArray(spConfig.allowedProducts);

                    if( isShowColorSwatchInSideBar){
                        fixedOptions = item.find('.options li');
                    }

                    // Output fixed options
                    fixedOptions.each(function (i, o) {
                        var fixedOption = _(o).attr('option').split('-'),
                            attribute = fixedOption[0],
                            attributeValue = selectedOptions[attribute] || fixedOption[1],
                            valueLabel = attributeValue;

                        _.each(spConfig.attributes[attribute].options, function (i, v) {
                            if ( v.id == attributeValue ) {
                                valueLabel = v.label;
                                if( !isShowColorSwatchInSideBar ) {
                                    allowedProducts = _(allowedProducts).filter(v.products);
                                }
                                return false;
                            }
                        });

                        if( !isShowColorSwatchInSideBar ) {
                            optionContainer.append('<p>' + spConfig.attributes[attribute].label + ': ' +
                                valueLabel + '</p><input type="hidden" name="hamper_option[' + option + '][' + pickIndex +
                                '][' + selection + '][' + attribute + ']" value="' + attributeValue + '"/>');
                        }

                        if(!isShowColorSwatchInSideBar ) {
                            processedOptions[attribute] = true;
                        }
                    });

                    if(isShowColorSwatchInSideBar) {
                        optionContainer.append('<input type="hidden" name="hamper_option[' + option + '][' + pickIndex +
                            '][' + selection + '][' + 181 + ']" value=""/>');
                    }

                    // Output other options
                    spConfig.attributes = JSON.toArray(spConfig.attributes);
                    spConfig.attributes.sort(function(a,b) { return b.id - a.id} );

                    _.each(spConfig.attributes, function (attribute, v) {
                        // attributeId
                        attribute = v.id ;

                        if (!processedOptions[attribute]) {
                            var id = 'attr_' + pickIndex + '_' + attribute,
                                select = _('<select id="' + id + '" class="required-entry steps step1" name="hamper_option[' + option + '][' + pickIndex +
                                '][' + selection + '][' + attribute + ']" isVisible="1"></select>'),
                                selected = selectedOptions[attribute],
                                fixed = '<p class="steps step2 no-display">' + v.label + ': <span id="fixed_' + id + '"></span></p>',
                                inStock = 1;
                            select.data('allowed', allowedProducts);

                            select.append('<option value=""' + (selected ? '' : ' selected="selected"') + '>' + Translator.translate('Select %s').replace('%s', v.label) + '</option>');
                            _.each(v.options, function (i, o) {
                                if ( _(allowedProducts).filter(o.products).length)
                                {
                                    select.append('<option value="' + o.id + '"' + (selected == o.id? ' selected="selected"' : '') + ' allowed="' + o.products.join(',') + '">' + o.label + '</option>');
                                    (selected == o.id) && (targetProduct = _(allowedProducts).filter(o.products)[0]);
                                } else
                                    inStock = 0;
                            });

                            optionContainer.append(select).append(fixed);
                            inStock || optionContainer.prepend('<p class="sold-out-alert">' + Translator.translate('Sold out') + '</p>');

                            select.on('change', objThis.updateTargetProduct);
                            select.selectBox();
                            processedOptions[attribute] = true;
                        }
                    });
                    targetProduct = allowedProducts[0];
                }
                newItem.append('<input type="hidden" class="target-product" name="target_product[' + pickIndex + ']" value="' + targetProduct + '" />')
                    .append('<input type="hidden" name="target_parent_product[' + pickIndex + ']" value="' + product + '" />');
                this.pickIndex++;
                this.reloadPrice();
            }
        },
        checkMinItems: function () {
            var result = this.pickContainer.children(':visible').length >= this.config.minItems;
            if (result) {
                this.minItemsAlert.html('').hide();
            } else {
                this.minItemsAlert.html(Translator.translate('Please pick at least %s item(s)').replace('%s', this.config.minItems)).show();
            }
            return result;
        },
        formatPrice: function(price) {
            return formatCurrency(price, this.config.priceFormat);
        },
        goStep: function (step) {
            if (step == 2 && this.addToCartForm) {
                this.messageBox.find('.required-entry').removeClass('required-entry').attr('required-entry', '1');
                if (!this.checkMinItems() || !this.addToCartForm.validator.validate()) {
                    return;
                }
                this.messageBox.find('[required-entry=1]').addClass('required-entry');
            }
            _('.hamper-form .steps').hide();
            _('.hamper-form .step' + step).show();
            _('.hamper-form .right-col h3').removeClass('active');
            _('.hamper-form .right-col h3.s' + step).addClass('active');
        },
        pick: function (obj) {
            obj = _(obj);
            var option = obj.closest('ul').attr('option'),
                li = obj.closest('li'),
                selectionId = li.attr('selection');
            this.add(option, selectionId);
        },
        reloadPrice: function () {
            if (this.config.isFixedPrice) return 0;
            /** @type Product.OptionsPrice */
            var optionsPrice = this.config.optionsPrice,
                oldPrice = -optionsPrice.productOldPrice,
                calculatedPrice = -optionsPrice.productPrice,
                subtotal = 0,
                discount = 0,
                gift = false;
            this.pickContainer.children('li:visible').each(function(){
                var o = _(this), price = parseFloat(o.data('price'));
                oldPrice += price;
                calculatedPrice += price;
                subtotal += price;
            });
            // Apply discount
            _.each(this.config.discount, function (i, o) {
                if (subtotal >= o.price) {
                    discount = o.discount;
                    if (discount.slice(-1) == '%') {
                        discount = subtotal * (100 - parseFloat(discount)) / 100;
                    }
                    discount = parseFloat(discount);
                    return false;
                }
            });
            calculatedPrice -= discount;
            this.discountContainer.html(this.formatPrice(-discount));
            if (discount > 0) this.discountContainer.parent().show();
            else this.discountContainer.parent().hide();
            optionsPrice.changePrice('hamper', {'price': calculatedPrice, 'oldPrice': oldPrice});
            optionsPrice.reload();

            // Apply extra gifts
            _.each(this.config.gifts, function (i, o) {
                if (calculatedPrice >= o.price) {
                    gift = o.name;
                    return false;
                }
            });
            if (gift) this.giftContainer.html(this.giftContainer.attr('message').replace('%s', gift)).show();
            else this.giftContainer.html('').hide();

            return calculatedPrice;
        },
        remove: function (obj) {
            obj = _(obj);
            var li = obj.closest('li');
            li.siblings(':visible').length || _('.hamper-wrapper .short-desc').show();
            li.remove();
            this.reloadPrice();
        },
        updateTargetProduct: function (e) {
            var select = _(e.delegateTarget),
                option = select.find(':selected'),
                li = select.closest('li'),
                field = li.find('input.target-product'),
                //targetParentProduct = select.parent().parent().parent().find("input[name='target_parent_product[0]']"),
                targetParentProduct = field.next('input[type=hidden]'),
                spConfig = window['spConfig' + targetParentProduct.val()],
                allowedProducts = JSON.toArray(spConfig.allowedProducts) ;

            if( isShowColorSwatchInSideBar && select.attr('id')) {
                var attributeId = select.attr('id').split('_')[2],
                    otherAttributeLabel ;
                if(attributeId && attributeId == spConfig.attributeIdsToChange[0] ) {  // 181
                    var mainImage = jQuery("li[option='"+attributeId + '-' + option.val()+"']").attr('image');
                    select.parent().parent().prev().find('img').attr('src', mainImage);

                    // update input hidden hamper_option[3][0][52][181] value
                    select.parent().find("input[name='"+select.attr('name')+"']").val(option.val());

                    var sizeSelect;
                    _.each(spConfig.attributes, function (i, v) {
                        if(v.id != spConfig.attributeIdsToChange[0] ){
                            sizeSelect = jQuery('#attr_0_' + v.id).empty();
                            return false;
                        }
                    })

                    _.each(spConfig.attributes, function (i1, v1) {
                        if( v1.id == spConfig.attributeIdsToChange[0] ) { // color_code
                            _.each(spConfig.attributes, function (i2, v2) {
                                _.each(v2.options, function (i3, v3) {
                                allowedProducts = _(allowedProducts).filter(v3.products);
                                    _.each( spConfig.attributes, function (i4, v4) {
                                        if(v4.code != 'color_code') {  // size
                                            otherAttributeLabel = v4.label;
                                            _.each( v4.options, function (i5, v5) {
                                                if ( _(allowedProducts).filter(v5.products).length ) {
                                                    sizeSelect.append('<option value="' + v5.id + '" allowed="' + v5.products.join(',') + '">' + v5.label + '</option>');
                                                }
                                            })
                                        }
                                    })
                                })
                            })
                        }
                    })
                    sizeSelect.prepend('<option value="" allowed="" selected="selected">' + Translator.translate('Select %s').replace('%s', otherAttributeLabel) + '</option>');
            }

            // update target product input
            var attributesAllowedProducts = [];
            _.each(spConfig.attributes, function (i, v) {
                var optionValue = jQuery('#attr_0_'+ v.id).val();
                    attributesAllowedProducts[i] = jQuery('#attr_0_'+ v.id+' option[value='+optionValue+']').attr('allowed').split(',');
            })

            if( attributesAllowedProducts.length > 1 ) {
                attributesAllowedProducts = arrayIntersection(attributesAllowedProducts[0], attributesAllowedProducts[1]);
                attributesAllowedProducts = attributesAllowedProducts[0];
            }

            if(parseInt(attributesAllowedProducts)){
                field.val(parseInt(attributesAllowedProducts));
            }
        }

        //update target-product-id
        !isShowColorSwatchInSideBar && field.val(_(select.data('allowed')).filter(option.attr('allowed').split(','))[0]);
        _('#fixed_' + select.prop('id')).html(option.text());
        }
    };
})(jQuery);
