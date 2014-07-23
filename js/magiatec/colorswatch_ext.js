/**************************** EXTEND CONFIGURABLE PRODUCT **************************/
Product.Config = Class.create(Product.Config, {
    fillSelect: function(element, active){
        active = typeof active !== 'undefined' ? active : true;

        var attributeId = element.id.replace(/[a-z]*/, '');
        var isSwatchElement = this.config.attributeIdsToChange.indexOf(attributeId) != -1;
        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
//        element.options[0] = new Option("", '');

        var prevConfig = false;
        if(element.prevSetting&&element.prevSetting.selectedIndex){
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }

        if(options) {
            var index = 0, swatchesContainer = element.up().previous(),
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
                                height: this.config.swatchHeight
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
        this.initSwatch();
    }
});
