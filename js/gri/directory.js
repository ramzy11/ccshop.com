OriginalRegionUpdater = RegionUpdater;

RegionUpdater = Class.create(OriginalRegionUpdater, {
    initialize: function (countryEl, regionTextEl, regionSelectEl, regions, disableAction, zipEl) {
        var cities = regions.cities;
        this.cities = cities;
        this.cityTextEl = $(cities.cityTextEl);
        this.form = new VarienForm(this.cityTextEl.form);
        this.citySelectEl = $(cities.citySelectEl);
        this.citySelectEl.onchange = this.fillCity.bind(this);
        $(countryEl).onchange = function () {
            this.setCityOptional();
            this.updateLabels();
            this.update();
            jQuery(this.regionSelectEl).selectBox("refresh");
        }.bind(this);
        $(regionSelectEl).onchange = this.update.bind(this);
        this.addressTextEl = jQuery(this.form.form).find("input[id$=street1]")[0];
        delete regions.cities;
        OriginalRegionUpdater.prototype.initialize.apply(this, arguments);
        this.regionLabel = $$("label[for=" + this.regionSelectEl.id + "]")[0];
        this.cityLabel = $$("label[for=" + this.cityTextEl.id + "]")[0];
        this.setCityOptional();
        this.updateLabels();
    },
    update: function () {
        // Separate default value and selected value
        var defaultRegion = this.regionSelectEl.value || this.regionSelectEl.getAttribute("defaultValue");
        var selectedRegion = this.regionSelectEl.hasAttribute("hasSelected") ?
            (this.regionSelectEl.value || defaultRegion) : defaultRegion;
        this.regionSelectEl.setAttribute("hasSelected", "1");
        this.regionSelectEl.setAttribute("defaultValue", selectedRegion);
        OriginalRegionUpdater.prototype.update.apply(this, arguments);
        this.regionSelectEl.setAttribute("defaultValue", defaultRegion);

        // Treat selectBox as select
        var regionSelectBox = this.getRegionSelectBox();
        var citySelectBox = this.getCitySelectBox();
        if (!regionSelectBox) this.countryEl.style.cssText = "display:inline-block!important;";

        // Has region dropdown
        if (this.regions[this.countryEl.value]) {
            this.regionSelectEl.setAttribute("isVisible", "1");
            if (regionSelectBox && regionSelectBox.hasClassName("selectBox")) {
                regionSelectBox.style.display = "inline-block";
                this.regionSelectEl.style.cssText = "";
            }
            else this.regionSelectEl.style.cssText = "display:inline-block!important;";
            jQuery(this.regionSelectEl).selectBox("value", selectedRegion);
            if (this.regions[this.countryEl.value][this.regionSelectEl.value]) {
                this.regionTextEl.value = this.regions[this.countryEl.value][this.regionSelectEl.value].name;
            }
        }
        // No region dropdown
        else {
            this.regionSelectEl.removeAttribute("hasSelected");
            this.regionSelectEl.setAttribute("isVisible", "");
            if (regionSelectBox && regionSelectBox.hasClassName("selectBox")) {
                jQuery(this.regionSelectEl).selectBox("refresh");
                regionSelectBox.style.display = "none";
            }
            this.regionSelectEl.value = "";
            this.regionSelectEl.style.cssText = "";
        }

        // Has city dropdown
        if (this.cities[this.countryEl.value] && this.cities[this.countryEl.value][this.regionSelectEl.value]) {
            var i, option, city, cityId, def, selectedValue;
            this.citySelectEl.setAttribute("isVisible", "1");
            def = this.citySelectEl.getAttribute("defaultValue");
            if (this.cityTextEl) {
                if (!def) {
                    def = this.cityTextEl.value;
                }
                this.cityTextEl.value = "";
            }
            def = def.toLowerCase();
            this.citySelectEl.options.length = 1;
            for (cityId in this.cities[this.countryEl.value][this.regionSelectEl.value]) {
                city = this.cities[this.countryEl.value][this.regionSelectEl.value][cityId];

                option = document.createElement("OPTION");
                option.value = cityId;
                option.text = city.name.stripTags();
                option.title = city.name;

                if (this.citySelectEl.options.add) {
                    this.citySelectEl.options.add(option);
                } else {
                    this.citySelectEl.appendChild(option);
                }

                if (cityId==def || (city.name && city.name.toLowerCase()==def) ||
                    (city.code && city.code.toLowerCase()==def)
                    ) {
                    this.citySelectEl.value = selectedValue = cityId;
                    this.cityTextEl.value = city.name;
                }
            }
            this.cityTextEl.style.display = "none";
            if (citySelectBox && citySelectBox.hasClassName("selectBox")) {
                jQuery(this.citySelectEl).selectBox("refresh").selectBox("value", selectedValue);
                citySelectBox.style.display = "inline-block";
                this.citySelectEl.style.cssText = "";
            }
            else this.citySelectEl.style.cssText = "display:inline-block!important;";
        }
        // No city dropdown
        else {
            this.citySelectEl.setAttribute("isVisible", "");
            if (citySelectBox && citySelectBox.hasClassName("selectBox")) {
                jQuery(this.citySelectEl).selectBox("refresh");
                citySelectBox.style.display = "none";
            }
            this.citySelectEl.value = "";
            this.cityTextEl.style.display = "";
            this.citySelectEl.style.cssText = "";
        }
//        this.form.validator.validate();
    },
    fillCity: function () {
        if (this.citySelectEl.value) {
            this.citySelectEl.setAttribute("defaultValue", this.citySelectEl.value);
            this.cityTextEl.value = this.citySelectEl.options[this.citySelectEl.selectedIndex].text;
            if (this.getCity() && jQuery.inArray(this.countryEl.value, optionalZipCountries) == -1) {
                this.zipEl.value = this.getCity().code;
            }

            // Self pick-up address
            if (this.regions[this.countryEl.value][this.regionSelectEl.value].code == "PICKUP") {
                this.addressTextEl.value = RegionUpdater.selfPickUpAddress;
            }
        }
//        this.form.validator.validate();
    },
    getCity: function () {
        return (this.cities[this.countryEl.value] &&
            this.cities[this.countryEl.value][this.regionSelectEl.value] &&
            this.cities[this.countryEl.value][this.regionSelectEl.value][this.citySelectEl.value]
        ) ? this.cities[this.countryEl.value][this.regionSelectEl.value][this.citySelectEl.value] : false;
    },
    getCitySelectBox: function() {
        var citySelectBox = this.citySelectEl.previous();
        if (!citySelectBox || !citySelectBox.hasClassName('selectBox')) {
            jQuery(this.citySelectEl).selectBox();
            citySelectBox = this.citySelectEl.previous();
        }
        return citySelectBox;
    },
    getRegionSelectBox: function() {
        var regionSelectBox = this.regionSelectEl.previous();
        if (!regionSelectBox || !regionSelectBox.hasClassName('selectBox')) {
            jQuery(this.regionSelectEl).selectBox();
            regionSelectBox = this.regionSelectEl.previous();
        }
        return regionSelectBox;
    },
    setCityOptional: function() {
        if (!this.cityLabel) return;
        var wildCard = this.cityLabel.down('em') || this.cityLabel.down('span.required'),
            fieldCity = this.cityTextEl.up('.field-city');

        // Make City and its label required/optional
        if (optionalCityCountries.indexOf(this.countryEl.value) != -1) {
            while (this.cityTextEl.hasClassName('required-entry')) {
                this.cityTextEl.removeClassName('required-entry');
            }
            this.cityLabel.removeClassName('required');
            if (wildCard != undefined) {
                wildCard.hide();
            }
            fieldCity.hide();
            this.cityTextEl.value = "";
        } else {
            this.cityTextEl.addClassName('required-entry');
            this.cityLabel.addClassName('required');
            if (wildCard != undefined) {
                wildCard.show();
            }
            fieldCity.show();
        }
    },
    updateLabels: function() {
        var regionSpan = this.regionLabel.down('span'), citySpan = this.cityLabel.down('span');
        if (!this.regionLabel.getAttribute('default')) this.regionLabel.setAttribute('default', regionSpan.innerHTML);
        if (!this.cityLabel.getAttribute('default')) this.cityLabel.setAttribute('default', citySpan.innerHTML);
        regionSpan.update(window.regionLabels && regionLabels[this.countryEl.value] ?
            regionLabels[this.countryEl.value] : this.regionLabel.getAttribute('default')
        );
        citySpan.update(window.cityLabels && cityLabels[this.countryEl.value] ?
            cityLabels[this.countryEl.value] : this.cityLabel.getAttribute('default')
        );
    }
});
