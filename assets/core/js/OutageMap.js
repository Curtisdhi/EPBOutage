function OutageMap(mapElement,metricsElement, zoom, centerLocation) {
    this.mapElement = mapElement;
    this.zoom = zoom;
    this.centerLocation = centerLocation;
    this.centerLocation.lat = parseFloat(this.centerLocation.lat);
    this.centerLocation.lng = parseFloat(this.centerLocation.lng);
    this.map = null;
    this.data = null;
    this.infowindows = [];
    this.metricsElement = metricsElement;
    this.metricsTemplate = metricsElement.find('.metric-template').clone();
    this.overlays = [];
    this.metricsTemplate.removeClass('metric-template hide');
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
    
    loadOutageDataFromRemote: function(id) {
        var _self = this;
        var route = null;
        if (_.isUndefined((id))) {
            route = Routing.generate('ajax_fetch_current_outage');
        } else {
            route = Routing.generate('ajax_fetch_outage', {id: id});
        }
        $.post(route).done(function(data) {
            if (!_.isUndefined((id))) {
                _self.saveOutageData(id, data);
            }
            _self.drawData(data);
        });
    },
    
    saveOutageData: function(id, data) {
        sessionStorage.setItem('outage_'+ id, JSON.stringify(data));
    },
    
    loadOutageData: function(id) {
        var _self = this;
        _self.clearOverlays();
        
        if (_.isUndefined((id))) {
            _self.loadOutageDataFromRemote();
        } else {
            var data = JSON.parse(sessionStorage.getItem('outage_'+ id));
            if (!data) {
                _self.loadOutageDataFromRemote(id);
            } else {
                _self.drawData(data);
            }
        }
    },
    
    clearOverlays: function() {
        var _self = this;
        _.each(_self.overlays, function(v)  {
            v.setMap(null);
        });
        _self.overlays = [];
    },
    
    
    drawData: function(data) {
        var _self = this;
        _self.data = data;
        _.each(data.boundaries, function(v) {
            var districtOutage = _.find(data.districtOutages, function(o) { return v.name === o.name });
            _self.overlays.push(_self.drawBoundary(v, districtOutage, v.latLng, '#3494d3', 0.2, 1));
        });
        _.each(data.dispatches, function(v) {
            var center = {lat: parseFloat(v.latitude), lng: parseFloat(v.longitude)};
            _self.overlays.push(_self.drawDispatchesOutages(v, center, '#FF0000', 0.5, 1, v.customerQty));
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
            
            var mapLabel = new MapLabel({
                text: boundary.name,
                position: bounds.getCenter(),
                map: _self.map,
                fontSize: 12,
                align: 'right'
            });
            
            _self.overlays.push(mapLabel);
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
        var openTimeout = null;
        var closeTimeout = null;
        
        object.addListener('mouseover', function () {
            if (!_.isNull(openTimeout)) {
                clearTimeout(openTimeout);
            }
            if (!_.isNull(closeTimeout)) {
                clearTimeout(closeTimeout);
            }
            openTimeout = setTimeout(function() {
                _.each(_self.infowindows, function(v) {
                    v.close();
                });
                infoWindow.open(_self.map, object);
                infoWindow.setPosition(position);
            }, 500);
            
        });
        
        object.addListener('mouseout', function() {
            if (!_.isNull(closeTimeout)) {
                clearTimeout(closeTimeout);
            }
            closeTimeout = setTimeout(function() { 
                infoWindow.close();
            }, 1000);
        });
        
        //add infowindows to the global space for management
        _self.infowindows.push(infoWindow);

    },
    
    displayMetrics: function(data) {
        var _self = this;
        var groupEl = null;
        _self.metricsElement.empty();
        
        groupEl = _self.createAccordionGroup('Global');
        groupEl.find('.metric-body').addClass('show');
        
        _self.addMetricToAccordionGroup(groupEl, 'Current Outages', data.metrics.currentOutages);
        //figure out what this value represents
        //_self.displayMetric('Duration of Outages', data.metrics.durationOutages);
        _self.addMetricToAccordionGroup(groupEl, 'Auto restored Outages', data.metrics.autoRestoredOutages);
        _self.addMetricToAccordionGroup(groupEl, 'Prevented Outages', data.metrics.preventedOutages);
        _self.addMetricToAccordionGroup(groupEl, 'Total smart grid activity', data.metrics.totalSmartGridActivity);
        
        var customersAffected = 0;
        var crewDispatched = 0;
        _.each(data.dispatches, function(v) {
            customersAffected += v.customerQty;
            crewDispatched += v.crewQty;
        });
        _self.addMetricToAccordionGroup(groupEl, 'Customers Affected', customersAffected);
        _self.addMetricToAccordionGroup(groupEl, 'Crew Dispatched', crewDispatched);
        
        var districtOutages = _.sortBy(data.districtOutages, 'name');
        _.each(districtOutages, function(v) {
            var disable = true;
            groupEl = _self.createAccordionGroup(v.name);
            if (v.incidents) {
                _self.addMetricToAccordionGroup(groupEl, 'Incidents', v.incidents);
                disable = false;
            }
            if (v.customersAffected) {
                _self.addMetricToAccordionGroup(groupEl, 'Customers Affected', v.customersAffected);
                disable = false;
            }
            if (disable) {
                groupEl.remove();
                //groupEl.find('.metric-title').attr('href', '').addClass('disabled');
            }
        });
        
        
    },
    
    createAccordionGroup: function(title) {
        var _self = this;
        var el = _self.metricsTemplate.clone();
        var id = 't' +(title +'_'+ (Math.floor(Math.random() * 10000))).hashCode();
        
        el.find('.metric-title').attr('href', '#'+id).text(title);
        el.find('.metric-body').attr('id', id);
        _self.metricsElement.append(el);
        
        return el;
    },
    
    addMetricToAccordionGroup: function(groupEl, label, value) {
        var _self = this;
        groupEl.find('table > tbody').append('<tr><th>'+ label +'</th><td>'+ value +'</td></tr>');
    }
};