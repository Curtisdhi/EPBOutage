function OutageMap(mapElement, zoom, centerLocation) {
    this.mapElement = mapElement;
    this.zoom = zoom;
    this.centerLocation = centerLocation;
    this.centerLocation.lat = parseFloat(this.centerLocation.lat);
    this.centerLocation.lng = parseFloat(this.centerLocation.lng);
    this.map = null;
    
}

OutageMap.prototype = {
    init: function() {
        var _self = this;
        _self.initialized = true;
    },
    
    load: function() {
        var _self = this;
        if (!_self.initialized) {
            _self.init();
        }
        console.log(_self.centerLocation);
        _self.map = new google.maps.Map(_self.mapElement[0], {
            zoom: _self.zoom,
            center: _self.centerLocation
        });
      
    }
};