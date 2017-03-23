$(document).ready(function () {
    var map = $('#map');
    var metricsTableElement = $('#metrics-accordion');
    outageMap = new OutageMap(map, metricsTableElement, map.data('zoom'), map.data('center-location'));
    outageMap.load();

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
    
    map.on('outage:loaded', function(e, data) {
        var url = Routing.generate('main_index', {id: data.id}, true);
        $('.btn-share').data('href', url); 
    });
    
    $('.btn-share').click(function(e) {
        var btn = $(this);
        
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(btn.data('href')).select();
        document.execCommand("copy");
        $temp.remove();
        
        setTimeout(function() {
            btn.tooltip('hide');
        }, 2000);
    }).tooltip({
        trigger: 'click'
    });


    var id = $('select[name="outage-picker"]').val();
    outageMap.loadOutageData(id);
});