export default class OutageMap {
    constructor(mapElement,metricsElement, zoom, centerLocation) {
        this.mapElement = mapElement;
        this.zoom = zoom;
        this.centerLocation = centerLocation;
        this.centerLocation.lat = parseFloat(this.centerLocation[0]);
        this.centerLocation.lng = parseFloat(this.centerLocation[1]);
        this.map = null;
        this.data = null;
        this.infowindows = [];
        this.metricsElement = metricsElement;
        this.metricsTemplate = metricsElement.find('.metric-template').clone();
        this.overlays = [];
        this.metricsTemplate.removeClass('metric-template hide');
        this.boundaryColor = '#3494d3';
        this.dispatchJobInfo = {
            colors: {
                DEFAULT: '#FF0000',
                OUTAGE_REPORTED: '#FF0000',
                REPAIR_IN_PROGRESS: '#ff8c4f'
            },
            userFriendlyName: {
                OUTAGE_REPORTED: 'Outage Reported',
                REPAIR_IN_PROGRESS: 'Repair in Progress'
            }
        };
    }

    init() {
        let _self = this;
        _self.initialized = true;
        _self.numberFormater = new Intl.NumberFormat();
    }

    load() {
        let _self = this;
        if (!_self.initialized) {
            _self.init();
        }
        _self.map = new google.maps.Map(_self.mapElement[0], {
            zoom: _self.zoom,
            center: _self.centerLocation,
            mapTypeId: 'terrain'
        });
    }

    loadOutageDataFromRemote(id) {
        let _self = this;
        let route = null;
        if (_.isUndefined((id))) {
            route = Routing.generate('ajax_fetch_current_outage');
        } else {
            route = Routing.generate('ajax_fetch_outage', {id: id});
        }
        $.post(route).done(function(data) {
            _self.saveOutageData(data.id, data);
            _self.drawData(data);
            _self.mapElement.trigger('outage:loaded', data);
        });
    }

    saveOutageData(id, data) {
        sessionStorage.setItem('outage_'+ id, JSON.stringify(data));
    }

    loadOutageData(id) {
        let _self = this;
        _self.clearOverlays();

        if (_.isUndefined((id))) {
            _self.loadOutageDataFromRemote();
        } else {
            let data = JSON.parse(sessionStorage.getItem('outage_'+ id));
            if (!data) {
                _self.loadOutageDataFromRemote(id);
            } else {
                _self.drawData(data);
                _self.mapElement.trigger('outage:loaded', data);
            }
        }
    }

    clearOverlays() {
        let _self = this;
        _.each(_self.overlays, function(v)  {
            v.setMap(null);
        });
        _self.overlays = [];
    }


    drawData(data) {
        let _self = this;
        _self.data = data;
        _.each(data.boundaries, function(v) {
            let districtOutage = _.find(data.districtOutages, function(o) { return v.name === o.name; });
            _self.overlays.push(_self.drawBoundary(v, districtOutage, v.latLng, _self.boundaryColor, 0.2, 1));
        });
        _.each(data.dispatches, function(v) {
            let center = {lat: parseFloat(v.latitude), lng: parseFloat(v.longitude)};
            let color = _self.dispatchJobInfo.colors['DEFAULT'];
            if (!_.isUndefined(_self.dispatchJobInfo.colors[v.jobStatus])) {
              color = _self.dispatchJobInfo.colors[v.jobStatus];
            }
            _self.overlays.push(_self.drawDispatchesOutages(v, center, color, 0.5, 1, v.customerQty));
        });

        _self.displayMetrics(data);
    }

    drawBoundary(boundary, districtOutage, paths, color, opacity, strokeWeight) {
        let _self = this;
        let polygon = new google.maps.Polygon({
          paths: paths,
          strokeColor: EPBOutage.darkenHexColor(color),
          strokeOpacity: 1,
          strokeWeight: strokeWeight,
          fillColor: color,
          fillOpacity: opacity,
          map: _self.map
        });

        if (!_.isUndefined(districtOutage)) {
            let bounds = new google.maps.LatLngBounds();
            _.each(paths, function(v) {
                bounds.extend(v);
            });

            let mapLabel = new MapLabel({
                text: boundary.name,
                position: bounds.getCenter(),
                map: _self.map,
                fontSize: 12,
                align: 'right'
            });

            _self.overlays.push(mapLabel);
        }

        return polygon;
    }

    drawDispatchesOutages(dispatch, center, color, opacity, strokeWeight, numberOfOutages) {
        let _self = this;
        if (numberOfOutages < 10) { numberOfOutages = 10; }
        let radius = Math.sqrt(numberOfOutages) * 100;
        let circle = new google.maps.Circle({
            strokeColor: EPBOutage.darkenHexColor(color),
            strokeOpacity: 1,
            strokeWeight: strokeWeight,
            fillColor: color,
            fillOpacity: opacity,
            map: _self.map,
            center: center,
            radius: radius
          });

          let jobStatus = dispatch.jobStatus;
          if (!_.isUndefined(_self.dispatchJobInfo.userFriendlyName[dispatch.jobStatus])) {
            jobStatus = _self.dispatchJobInfo.userFriendlyName[dispatch.jobStatus];
          }
        let content = "<h5>Outage Info</h5><div><label>Job Status:</label> "+ jobStatus +"</div>"+
                "<div><label>Crew dispatched:</label> "+ _self.numberFormater.format(dispatch.crewQty) +"</div>"+
                "<div><label>Customers affected</label> "+ _self.numberFormater.format(dispatch.customerQty) +"</div>";

        _self.addInfowindow(circle, content, circle.getCenter());
        return circle;
    }

    addInfowindow(object, content, position) {
        let _self = this;
        let infoWindow = new google.maps.InfoWindow({
            content: content
        });
        let openTimeout = null;
        let closeTimeout = null;

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

    }

    displayMetrics(data) {
        let _self = this;
        let groupEl = null;
        _self.metricsElement.empty();

        groupEl = _self.createAccordionGroup('Global');
        groupEl.find('.metric-body').addClass('show');

        _self.addMetricToAccordionGroup(groupEl, 'Current Incidents', _self.numberFormater.format(data.metrics.currentOutages));
        //figure out what this value represents
        //_self.displayMetric('Duration of Outages', data.metrics.durationOutages);

        let reportedOutages = 0;
        let outagesRepairInProgress = 0;

        _.each(data.dispatches, function(v) {
            switch (v.jobStatus) {
              case 'OUTAGE_REPORTED':
                reportedOutages++;
                break;
              case 'REPAIR_IN_PROGRESS':
                outagesRepairInProgress++;
                break;
              }
        });

        _self.addMetricToAccordionGroup(groupEl, 'Reported Incidents', _self.numberFormater.format(reportedOutages));
        _self.addMetricToAccordionGroup(groupEl, 'Repair in Progresses', _self.numberFormater.format(outagesRepairInProgress));
        _self.addMetricToAccordionGroup(groupEl, 'Customers Affected', _self.numberFormater.format(data.metrics.customersAffected));
        _self.addMetricToAccordionGroup(groupEl, 'Crew Dispatched', _self.numberFormater.format(data.metrics.crewDispatched));

        _self.addMetricToAccordionGroup(groupEl,
          'Smart Grid Restores<br> <small class="text-muted">(last 24 hours)</small>',
            _self.numberFormater.format(data.metrics.smartGridRestores), true);
          _self.addMetricToAccordionGroup(groupEl,
            'Manual Restores<br> <small class="text-muted">(last 24 hours)</small>',
            _self.numberFormater.format(data.metrics.manualRestores), true);
        _self.addMetricToAccordionGroup(groupEl, 'Prevented Outages', _self.numberFormater.format(data.metrics.preventedOutages), true);
        _self.addMetricToAccordionGroup(groupEl, 'Auto restored Outages', _self.numberFormater.format(data.metrics.autoRestoredOutages), true);
        _self.addMetricToAccordionGroup(groupEl, 'Total smart grid activity', _self.numberFormater.format(data.metrics.totalSmartGridActivity), true);


        let districtOutages = _.sortBy(data.districtOutages, 'name');
        _.each(districtOutages, function(v) {
            let disable = true;
            groupEl = _self.createAccordionGroup(v.name);
            if (v.incidents) {
                _self.addMetricToAccordionGroup(groupEl, 'Incidents', _self.numberFormater.format(v.incidents));
                disable = false;
            }
            if (v.customersAffected) {
                _self.addMetricToAccordionGroup(groupEl, 'Customers Affected', _self.numberFormater.format(v.customersAffected));
                disable = false;
            }
            if (disable) {
                groupEl.remove();
                //groupEl.find('.metric-title').attr('href', '').addClass('disabled');
            }
        });


    }

    createAccordionGroup(title) {
        let _self = this;
        let el = _self.metricsTemplate.clone();
        let id = 't' +(title +'_'+ (Math.floor(Math.random() * 10000))).hashCode();

        el.find('.metric-title').attr('href', '#'+id).text(title);
        el.find('.metric-body').attr('id', id);
        _self.metricsElement.append(el);

        return el;
    }

    addMetricToAccordionGroup(groupEl, label, value, displayIfNotNull) {
        let _self = this;
        if (!displayIfNotNull || (displayIfNotNull && !_.isNull(value))) {
          groupEl.find('table > tbody').append('<tr><th>'+ label +'</th><td>'+ value +'</td></tr>');
        }
    }
}
