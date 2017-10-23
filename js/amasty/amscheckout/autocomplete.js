function amastyScheckoutInitAutocomplete() {

    var map = $H({
        country: [
            {
                key: 'short_name',
                input: 'country_id'
            }
        ],
        locality: [
            {
                key: 'long_name',
                input: 'city'
            }
        ],
        postal_town: [
            {
                key: 'long_name',
                input: 'city'
            }
        ],
        route: [
            {
                key: 'long_name',
                input: 'street1'
            }
        ],
        postal_code: [
            {
                key: 'long_name',
                input: 'postcode'
            }
        ],
        administrative_area_level_2: [
            {
                key: 'long_name',
                input: 'region'
            },
            {
                key: 'short_name',
                input: 'region_id'
            }
        ],
        administrative_area_level_1: [
            {
                key: 'long_name',
                input: 'region'
            },
            {
                key: 'short_name',
                input: 'region_id'
            }
        ]
    });

    Event.observe(window, "load", function() {
        $A(['billing', 'shipping']).each(function(area){
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            var autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById(area + ':street1')),
                {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', function(){
                fillInAddress.apply(autocomplete, [area])
            });
        });

    });

    function fillInAddress(area) {

        // Get the place details from the autocomplete object.
        var place = this.getPlace();
        if (place.name) {
            document.getElementById(area + ':street1').value = place.name;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.

        map.each(function(mapEntity){
            var addressType;

            var addressType = $H(place.address_components).find(function(value){
                return typeof(value.value) == 'object' && mapEntity.key == value.value.types[0];
            });

            if (addressType && addressType.value){
                mapEntity.value.each(function(config){
                    if (config.input === 'country_id'){
                        $(area + ':' + config.input).setValue(
                            addressType.value[config.key]
                        );

                        if (area === 'billing' && typeof(billingRegionUpdater) === 'object') {
                            billingRegionUpdater.update();
                        } else if (area === 'shipping' && typeof(shippingRegionUpdater) === 'object') {
                            shippingRegionUpdater.update();
                        }
                    } else if (config.input === 'region_id'){
                        var regionId = addressType.value['short_name'];
                        var region = addressType.value['long_name'];

                        updateRegionId(area, regionId, region)
                    } else {
                        $(area + ':' + config.input).setValue(
                            addressType.value[config.key]
                        );
                    }
                });
            }
        });

        updateCheckout('billing');
    }

    function updateRegionId(area, regionId, region){
        var updater;

        if (area == 'billing' && typeof(billingRegionUpdater) === 'object'){
            updater = billingRegionUpdater;
        } else if (typeof(shippingRegionUpdater) === 'object') {
            updater = shippingRegionUpdater;
        }

        if (updater){
            var country = updater.countryEl.getValue();

            if (updater.regions[country]) {
                var region = $H(updater.regions[country]).find(function (value, index) {
                    return value.value.code == regionId || value.value.code == region || value.value.name == region;
                });

                if (region && region.key) {
                    $(area + ':region_id').setValue(region.key);
                }
            }
        }
    }
}