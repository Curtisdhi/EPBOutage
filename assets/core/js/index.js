$(document).ready(function () {
    var map = $('#map');
    var metricsTableElement = $('#metrics-accordion');
    outageMap = new OutageMap(map, metricsTableElement, map.data('zoom'), map.data('center-location'));
    outageMap.load();

    //get rather or not an precise date has been set
    var startDateSet = $('#outage-datetime-picker').data('isset');
    
    $('#outage-datetime-picker').datetimepicker({
        format: 'M dd, yyyy hh:ii',
        minuteStep: 60
    }).on('changeDate', function(e){
        window.location = Routing.generate('main_index', {'start_date': (e.date.getTime() / 1000)});
    });
    
    $('select[name="outage-picker"], select[name="majorOutage"]').change(function() {
       outageMap.loadOutageData($(this).val()); 
    });
    
    $('.btn-metrics-toggler').click(function() {
        $(this).closest('.metrics-sidebar').find('.metrics').animate({width:'toggle'});
    });
    
    var loaded = false;
    if (startDateSet) {
        var id = $('select[name="outage-picker"]').val();
        if (id) {
            outageMap.loadOutageData(id);
            loaded = true;
        }
    } 
    
    if (!loaded) {
        outageMap.loadOutageData();
    }


});