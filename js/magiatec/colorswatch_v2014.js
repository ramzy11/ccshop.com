/**************************** EXTEND CONFIGURABLE PRODUCT **************************/
Product.Config = Class.create(Product.Config, {
    initialize: function(config){
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
        if (config.containerId) {
            this.settings   = $$('#' + config.containerId + ' ' + '.super-attribute-select');
        } else {
            this.settings   = $$('.super-attribute-select');
        }
        this.state      = new Hash();
        this.priceTemplate = new Template(this.config.template);
        this.prices     = config.prices;

        // Set default values from config
        if (config.defaultValues) {
            this.values = config.defaultValues;
        }

        // Overwrite defaults by url
        var separatorIndex = window.location.href.indexOf('#');
        if (separatorIndex != -1) {
            var paramsStr = window.location.href.substr(separatorIndex+1);
            var urlValues = paramsStr.toQueryParams();
            if (!this.values) {
                this.values = {};
            }
            for (var i in urlValues) {
                this.values[i] = urlValues[i];
            }
        } else {
            if (!this.values) {
                this.values = {};
            }
            var objThis = this;
            jQuery.each(config.attributes, function(attributeId, definition){
                if (config.attributeIdsToChange.indexOf(attributeId) != -1) {
                    definition.options.each(function(option){
                        if (option.products.length) {
                            objThis.values[attributeId] = option.id;
                            throw $break;
                        }
                    });
                    return false;
                }
            });
        }
        // Delete illegal default values
        for (var i in this.values) {
            var optionExistence = false;
            if (config.attributes[i] && config.attributes[i].options) {
                config.attributes[i].options.each(function(option) {
                    if (this.values[i] == option.id) {
                        optionExistence = true;
                        throw $break;
                    }
                }.bind(this));
            }
            if (!optionExistence) delete this.values[i];
        }

        // Overwrite defaults by inputs values if needed
        if (config.inputsInitialized) {
            this.values = {};
            this.settings.each(function(element) {
                if (element.value) {
                    var attributeId = element.id.replace(/[a-z]*/, '');
                    this.values[attributeId] = element.value;
                }
            }.bind(this));
        }

        // Put events to check select reloads
        this.settings.each(function(element){
            jQuery(element).change(this.configure.bind(this));
        }.bind(this));

        // fill state
        this.settings.each(function(element){
            var attributeId = element.id.replace(/[a-z]*/, '');
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this))

        // Init settings dropdown
        var childSettings = [];
        for(var i=this.settings.length-1;i>=0;i--){
            var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
            var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;

            /*if (i == 0){
                this.fillSelect(this.settings[i])
            } else {
                this.settings[i].disabled = true;
            }*/

            active = (i==0) ? true : false;
            this.fillSelect(this.settings[i], active);

            $(this.settings[i]).childSettings = childSettings.clone();
            $(this.settings[i]).prevSetting   = prevSetting;
            $(this.settings[i]).nextSetting   = nextSetting;
            childSettings.push(this.settings[i]);
        }

        // Set values to inputs
        this.configureForValues();
        document.observe("dom:loaded", this.configureForValues.bind(this));
    },

    configureElement : function(element) {
        if( element.selectedIndex == -1){
            element.selectedIndex = 0;
        }

        this.reloadOptionLabels(element);
        if(element.value){
            var attributeId = element.config.id;
            this.state[attributeId] = element.value;
            if(element.nextSetting){
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
                jQuery(element.nextSetting).selectBox("refresh");
            }
            if (this.config.imageSwitcher.enabled && this.config.attributeIdsToChange.indexOf(attributeId) != -1) {
                this.changeImages(element);
            }
        }
        else {
            this.resetChildren(element);
            if (element.nextSetting) {
                this.fillSelect(element.nextSetting, false);
            }
        }
        this.reloadPrice();
    },

    fillSelect: function(element, active){
        active = typeof active !== 'undefined' ? active : true;

        var attributeId = element.id.replace(/[a-z]*/, '');
        var isSwatchElement = this.config.attributeIdsToChange.indexOf(attributeId) != -1;
        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option("", '');

        var prevConfig = false;
        if(element.prevSetting&&element.prevSetting.selectedIndex){
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }

        if(options && this.config.attributes[attributeId].code != 'size_shoes' && this.config.attributes[attributeId].code != 'size_clothing') {
            var index = 1, swatchesContainer = element.up().previous(),
                ul_el = new Element('ul'), li_el, a_el, img_el;

            for(var i=0;i<options.length;i++){
                var allowedProducts = [];
                if(prevConfig) {
                    for(var j=0;j<options[i].products.length;j++){
                        if(prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.clone();
                }

                if(allowedProducts.size()>0){
                    options[i].allowedProducts = allowedProducts;
                    element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }

                if(isSwatchElement) {
                    if (allowedProducts.size()>0||(allowedProducts.size()==0&&this.config.showNotAvailable)) {
                        if (options[i].swatch) {
                            img_el = new Element('img', {
                                src: options[i].swatch,
                                width: this.config.swatchWidth,
                                height: this.config.swatchHeight,
                                title: options[i].label
                            });
                        }
                        else {
                            img_el = new Element('span');
                            img_el.update(options[i].label);
                        }
                        a_el   = new Element('a', {
                            href: 'javascript:void(0);',
                            rel: options[i].id
                        }).insert({bottom: img_el});

                        a_el.observe('click', function(event) {
                            var a_clicked = Event.findElement(event, 'a'), li = a_clicked.up();

                            if (li.hasClassName('disabled')) return false;

                            if (li.hasClassName('active')) {
                                li.removeClassName('active');
                                element.value = "";
                            } else {
                                li.addClassName('active').siblings().each(function(el) {
                                    el.removeClassName('active');
                                });
                                element.value = a_clicked.readAttribute('rel');
                            }
                            this.configureElement(element);
                        }.bind(this));

                        li_class = (active&&allowedProducts.size()>0) ? '' : 'disabled';
                        li_el  = new Element('li').addClassName(li_class).insert({bottom: a_el});
                        if (this.values && this.values[attributeId] == options[i].id) li_el.addClassName('active');
                        if (!options[i].swatch) {
                            li_el.addClassName('flat');
                        }

                        ul_el.insert({bottom: li_el});
                    }
                }
                else {
                    if (allowedProducts.size()>0||(allowedProducts.size()==0&&this.config.showNotAvailable)) {
                        a_el   = new Element('a', {
                            href: 'javascript:void(0);',
                            rel: options[i].id
                        }).update(options[i].label).insert({bottom: img_el});

                        li_class = (active&&allowedProducts.size()>0) ? '' : 'disabled';
                        li_el  = new Element('li').addClassName(li_class).insert({bottom: a_el});
                        if (this.values && this.values[attributeId] == options[i].id) li_el.addClassName('active');

                        li_el.observe('click', function(event) {
                            var li = Event.findElement(event, 'li');

                            if (li.hasClassName('disabled')) return false;

                            if (li.hasClassName('active')) {
                                li.removeClassName('active');
                                element.value = "";
                            } else {
                                li.addClassName('active').siblings().each(function(el) {
                                    el.removeClassName('active');
                                });
                                element.value = li.getElementsByTagName('a')[0].readAttribute('rel');
                            }
                            this.configureElement(element);
                        }.bind(this));

                        ul_el.insert({bottom: li_el});
                    }
                }
            }
            if (swatchesContainer.select('ul').size()>0) {
                swatchesContainer.select('ul').each(function(el) {
                    el.remove();
                });
            }
            isSwatchElement || ul_el.addClassName('flat-options');
            swatchesContainer.insert({top: ul_el});
        }

        if(options && (this.config.attributes[attributeId].code == 'size_shoes' || this.config.attributes[attributeId].code == 'size_clothing'))  {
            var index = 1;
            var dropdownContainer = element.up();
            dropdownContainer.writeAttribute('style','display:block');

            element.options[0] = new Option(this.config.attributes[attributeId].label,'');

            for(var i=0;i<options.length;i++){
                var allowedProducts = [];
                if(prevConfig) {
                    for(var j=0;j<options[i].products.length;j++){
                        if(prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.clone();
                }

                if(allowedProducts.size()>0){
                    options[i].allowedProducts = allowedProducts;
                    element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                    //console.log(this.getOptionLabel(options[i], options[i].price));
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }
            }
        }


        this.initSwatch();
    },

    reloadPrice: function(){
        if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if(selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        this.config.optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        this.config.optionsPrice.reload();

        return price;

        if($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
    },

    changeImages: function(element) {
        var swatchValue = element.value, currentSwatch, firstImage;
        jQuery('#magiatecolorswatch-gallery-wrapper div[swatch]').hide();
        if ((currentSwatch = jQuery('#magiatecolorswatch-gallery-wrapper .swatch-' + swatchValue))[0]) {
            currentSwatch.show();
            currentSwatch.find('li a:first').click();
        }
        this.settings.currentSwatch = swatchValue;
    },

    initSwatch: function() {
        var attributeId = this.config.attributeIdsToChange[0];
        if (!attributeId || this.settings.swatchInitialized) return;
        var value = 0, options, firstOption;
        options = this.config.attributes[attributeId].options;
        if (this.values && this.values[attributeId])
            value = this.values[attributeId];
        else if (options[0]) value = options[0].id;
        else if (firstOption = jQuery('.magiatecolorswatch-gallery li')[0])
            value = firstOption.getAttribute('swatch');
        this.changeImages({value: value});
        var currentSwatch = jQuery('#magiatecolorswatch-gallery-wrapper .swatch-' + value), firstThumb;
        if (currentSwatch[0]) {
            firstThumb = eval('(' + currentSwatch.find('li a:first').attr('rel') + ')') || {};
            jQuery('#magiatecolorswatch-anchor').attr('href', firstThumb.largeimage);
            jQuery('#magiatecolorswatch-image').attr('src', firstThumb.smallimage);
        }
        this.settings.swatchInitialized = true;
    }
});
