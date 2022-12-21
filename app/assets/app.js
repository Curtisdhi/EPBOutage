"use strict";

import $ from 'jquery';

import './styles/app.scss';
import OutageMap from './OutageMap';



$(document).ready(() => {
    var map = $('#map');
    var metricsTableElement = $('#metrics-accordion');
    var centerCoords = map.data('center-location').split(' ');

    let outageMap = new OutageMap(map, metricsTableElement, map.data('zoom'), centerCoords);
});

function initMap() {
    let map = new google.maps.Map(document.getElementById("map"), {
      center: { lat: -34.397, lng: 150.644 },
      zoom: 8,
    });
  }
  
window.initMap = initMap;