function OutageMap(mapElement, zoom, centerLocation) {
    this.mapElement = mapElement;
    this.zoom = zoom;
    this.centerLocation = centerLocation;
    this.centerLocation.lat = parseFloat(this.centerLocation.lat);
    this.centerLocation.lng = parseFloat(this.centerLocation.lng);
    this.map = null;
    this.data = null;
    
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
        _self.map = new google.maps.Map(_self.mapElement[0], {
            zoom: _self.zoom,
            center: _self.centerLocation,
            mapTypeId: 'terrain'
        });
        
    },
    
    loadOutageData: function(url) {
        var _self = this;
        $.post(Routing.generate('ajax_fetch_current_outage'))
            .done(function(data) {
                   _self.drawData(data);
            });
    },
    
    
    drawData: function(data) {
        var _self = this;
        _self.data = data;
        _.each(data.boundaries, function(v) {
            _self.drawBoundary(v.name, v.latLng, '#3494d3', 0.2, 1);
        });
        _.each(data.dispatches, function(v) {
            var center = {lat: parseFloat(v.latitude), lng: parseFloat(v.longitude)};
            _self.drawDispatchesOutages(center, '#FF0000', 0.5, 1, v.customerQty);
        });
    },
    
    drawBoundary: function(name, paths, color, opacity, strokeWeight) {
        var _self = this;
        var polygon = new google.maps.Polygon({
          paths: paths,
          strokeColor: EPBOutage.darkenHexColor(color),
          strokeOpacity: 1,
          strokeWeight: strokeWeight,
          fillColor: color,
          fillOpacity: opacity,
          map: _self.map
        });
    },
    
    drawDispatchesOutages: function(center, color, opacity, strokeWeight, numberOfOutages) {
        var _self = this;
        if (numberOfOutages < 10) { numberOfOutages = 10; }
        var radius = Math.sqrt(numberOfOutages) * 100;
        var circle = new google.maps.Circle({
            strokeColor: EPBOutage.darkenHexColor(color),
            strokeOpacity: 1,
            strokeWeight: strokeWeight,
            fillColor: color,
            fillOpacity: opacity,
            map: _self.map,
            center: center,
            radius: radius
          });
    }
};