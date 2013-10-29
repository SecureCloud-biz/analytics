$(document).ready(function() {
    charts = $('.analyticsChart');

    charts.each(function(k,v) {
        var html = $('.data', v).html();

        var json = $.parseJSON(html);

        Craft.postActionRequest('analytics/charts/parse', json, function(response) {

            response = $.parseJSON(response);

            $(v).dchart(response, json);

        });
    });
});