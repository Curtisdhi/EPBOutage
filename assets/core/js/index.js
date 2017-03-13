$(document).ready(function () {
    var map = $('#map');
    var metricsTableElement = $('#metrics-accordion');
    outageMap = new OutageMap(map, metricsTableElement, map.data('zoom'), map.data('center-location'));
    outageMap.load();
    outageMap.loadOutageData();

    var mySlider = $("input[name='outage-picker']");
    var latestOutages = mySlider.data('latest-outages');

    var ticks = [];
    var ticksLabels = [];

    var tick = 0;
    _.each(latestOutages, function (v) {
        ticks.push(++tick);
        ticksLabels.push(v.updatedOnFormatted);
    });

    mySlider.slider({
        min: 1,
        max: ticks.length,
        step: 1,
        value: ticks.length,
        ticks: ticks,
        ticks_labels: ticksLabels,
        ticks_tooltip: true
    }).change(function () {
        var id = getIdFromOutagesByIndex(parseInt($(this).val()));
        if (id) {
            outageMap.loadOutageData(id);
        }
    });
    
    $('select[name="majorOutage"]').change(function() {
       outageMap.loadOutageData($(this).val()); 
    });

    function getIdFromOutagesByIndex(index) {
        var i = 1;
        var o = null;
        _.each(latestOutages, function (v) {
            if (i === index) {
                o = v;
                return false;
            }
            i++;
        });
        return o ? o._id.$id : null;
    }


});