function OutageMap(mapElement,metricsTableElement, zoom, centerLocation) {
    this.mapElement = mapElement;
    this.zoom = zoom;
    this.centerLocation = centerLocation;
    this.centerLocation.lat = parseFloat(this.centerLocation.lat);
    this.centerLocation.lng = parseFloat(this.centerLocation.lng);
    this.map = null;
    this.data = null;
    this.infowindows = [];
    this.metricsTableElement = metricsTableElement;
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
            var districtOutage = _.find(data.districtOutages, function(o) { return v.name === o.name });
            var boundary = _self.drawBoundary(v, districtOutage, v.latLng, '#3494d3', 0.2, 1);
        });
        _.each(data.dispatches, function(v) {
            var center = {lat: parseFloat(v.latitude), lng: parseFloat(v.longitude)};
            _self.drawDispatchesOutages(v, center, '#FF0000', 0.5, 1, v.customerQty);
        });
        
        _self.displayMetrics(data);
    },
    
    drawBoundary: function(boundary, districtOutage, paths, color, opacity, strokeWeight) {
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
        
        if (!_.isUndefined(districtOutage)) {
            var content = "<h5>"+ boundary.name +"</h5><div>";
            if (districtOutage.incidents) {
                content += "<div><label>Incidents:</label> "+ districtOutage.incidents +"</div>";
            }
            if (districtOutage.customersAffected) {
                content += "<div><label>Customers affected</label> "+ districtOutage.customersAffected +"</div>";
            }
            
            var bounds = new google.maps.LatLngBounds();
            _.each(paths, function(v) {
                bounds.extend(v);
            });

            _self.addInfowindow(polygon, content, bounds.getCenter());
        }
        
        return polygon;
    },
    
    drawDispatchesOutages: function(dispatch, center, color, opacity, strokeWeight, numberOfOutages) {
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
          
        var content = "<h5>Outage Info</h5><div><label>Job Status:</label> "+ dispatch.jobStatus +"</div>"+
                "<div><label>Crew dispatched:</label> "+ dispatch.crewQty +"</div>"+
                "<div><label>Customers affected</label> "+ dispatch.customerQty +"</div>";

        _self.addInfowindow(circle, content, circle.getCenter());
        return circle;
    },
    
    addInfowindow: function(object, content, position) {
        var _self = this;
        var infoWindow = new google.maps.InfoWindow({
            content: content
        });
        var timeout = null;
        
        object.addListener('mouseover', function () {
            if (!_.isNull(timeout)) {
                clearTimeout(timeout);
            }
            timeout = setTimeout(function() {
                _.each(_self.infowindows, function(v) {
                    v.close();
                });
                infoWindow.open(_self.map, object);
                infoWindow.setPosition(position);
            }, 500);
            
        });
        
        //add infowindows to the global space for management
        _self.infowindows.push(infoWindow);

    },
    
    displayMetrics: function(data) {
        var _self = this;
        _self.metricsTableElement.empty();
        _self.displayMetric('Current Outages', data.metrics.currentOutages);
        //figure out what this value represents
        //_self.displayMetric('Duration of Outages', data.metrics.durationOutages);
        _self.displayMetric('Auto restored Outages', data.metrics.autoRestoredOutages);
        _self.displayMetric('Prevented Outages', data.metrics.preventedOutages);
        _self.displayMetric('Total smart grid activity', data.metrics.totalSmartGridActivity);
        
        var customersAffected = 0;
        var crewDispatched = 0;
        _.each(data.dispatches, function(v) {
            customersAffected += v.customerQty;
            crewDispatched += v.crewQty;
        });
        _self.displayMetric('Customers Affected', customersAffected);
        _self.displayMetric('Crew Dispatched', crewDispatched);
        
        
        
    },
    
    displayMetric: function(label, value) {
        var _self = this;
        _self.metricsTableElement.append('<tr><th>'+ label +'</th><td>'+ value +'</td></tr>');
    }
};