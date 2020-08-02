/*
Copyright (C) License Management Schiavon Maikol

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

var statusChartEle = document.getElementById("statusChart");
if(statusChartEle){
    var ctx = statusChartEle.getContext('2d');

    var labels = [langvars.status_1,langvars.status_2,langvars.status_3];
    var backgroundColor = ['rgba(234, 28, 13, 1)','rgba(255, 206, 86, 1)','rgba(76, 175, 80, 1)'];

    if(typeof(jQuery(".n_service_1").html()) != 'undefined'
        && typeof(jQuery(".n_service_2").html()) != 'undefined'
        && typeof(jQuery(".n_service_3").html()) != 'undefined' ){
         var n_service_1 = parseInt(jQuery(".n_service_1").html());
         var n_service_2 = parseInt(jQuery(".n_service_2").html());
         var n_service_3 = parseInt(jQuery(".n_service_3").html());

         var data = [n_service_1, n_service_2, n_service_3];
    }else if(typeof(jQuery("#n_servide_data").val()) != 'undefined'){
        var n_servide_data = JSON.parse( jQuery("#n_servide_data").val() );
        var servicename = Object.keys(n_servide_data);

        var n_service_1 = parseInt(n_servide_data[1]);
        var n_service_2 = parseInt(n_servide_data[2]);
        var n_service_3 = parseInt(n_servide_data[3]);
        var n_deadline = parseInt(n_servide_data['deadline']);

        labels.push( langvars.Deadline );
        var data = [n_service_1, n_service_2, n_service_3,n_deadline];
        //var backgroundColor = ['rgba(234, 28, 13, 1)','rgba(255, 235, 59, 1)','rgba(76, 175, 80, 1)','rgb(255, 152, 0, 1)'];
        backgroundColor.push( 'rgb(255, 152, 0, 1)' );
    }

    var legend_position = 'right';
    if(jQuery("#dashboard").length > 0){
        legend_position = 'left';
    }

    // And for a doughnut chart
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: data,
                backgroundColor: backgroundColor,    
            }],
    
            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: labels
        },
        options: {
                responsive: true,
                legend: {
                    position: legend_position,
                },
                title: {
                    display: false,
                    text: 'Chart.js Doughnut Chart'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
    });
}

var infoStatusServiceChartEle = document.getElementById("infoStatusServiceChart");
var statusServiceChartEle = document.getElementById("statusServiceChart");
if(infoStatusServiceChartEle && statusServiceChartEle){

    var chart_value = JSON.parse( jQuery("#infoStatusServiceChart").val() );
       
    var data_chart_presentare = [];
    var data_chart_lavorazione = [];
    var data_chart_completato = [];
    var servicename = Object.keys(chart_value);
    for(var i = 0; i < servicename.length; i++){
        data_chart_presentare.push( chart_value[servicename[i]][1] );
        data_chart_lavorazione.push( chart_value[servicename[i]][2] );
        data_chart_completato.push( chart_value[servicename[i]][3] );
    }

    var barChartData = {
        labels: servicename,
        datasets: [{
            label: langvars.status_1,
            backgroundColor: 'rgba(234, 28, 13, 1)',
            stack: 'Stack 0',
            data: data_chart_presentare
        }, {
            label: langvars.status_2,
            backgroundColor: 'rgba(255, 206, 86, 1)',
            stack: 'Stack 0',
            data: data_chart_lavorazione
        }, {
            label: langvars.status_3,
            backgroundColor: 'rgba(76, 175, 80, 1)',
            stack: 'Stack 0',
            data: data_chart_completato
        }]

    };
    window.onload = function() {
        var ctx = statusServiceChartEle.getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                title: {
                    display: true,
                    text: langvars.LBL_STATE_SERVICES
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            }
        });
    };

}
/*
var infoAnalytics = document.getElementById("infoAnalytics");
var analyticsChart = document.getElementById("analyticsChart");
if(infoAnalytics && analyticsChart){

    var analytics_value = JSON.parse( infoAnalytics.value );
    if(analytics_value != ''){
       var chart_value_2 = Object.values(analytics_value);
    }

    var MONTHS = [langvars.January, langvars.February, langvars.March, langvars.April, langvars.May, langvars.June, langvars.July, langvars.August, langvars.September, langvars.October, langvars.November, langvars.December];
    var color = Chart.helpers.color;
    var barChartData2 = {
        labels: MONTHS,
        datasets: [{
            label: langvars.Services,
            backgroundColor: color('#4CAF50').alpha(0.5).rgbString(),
            borderColor: "#4CAF50",
            borderWidth: 1,
            data: chart_value_2
        }]
    };

    var ctx2 = analyticsChart.getContext('2d');
    window.myBar2 = new Chart(ctx2, {
        type: 'bar',
        data: barChartData2,
        options: {
            responsive: true,
            legend: {
                display: false,
                position: 'top',
            },
            title: {
                display: true,
                text: langvars.LBL_CHART_COMPLETE_DATEEND
            }
        }
    });
}*/

var c_v_complete_dateend = document.getElementById("chartCompleteDateendVal");
var c_p_complete_dateend = document.getElementById("chartCompleteDateend");
license_managment_generate_barChartData2(c_v_complete_dateend,c_p_complete_dateend,langvars.LBL_CHART_COMPLETE_DATEEND);

var infoAnalytics = document.getElementById("infoAnalytics");
var analyticsChart = document.getElementById("analyticsChart");
license_managment_generate_barChartData2(infoAnalytics,analyticsChart,langvars.LBL_CHART_BECAME_COMPLETE);

function license_managment_generate_barChartData2(chart_value,chart_position,lbl_title){
    if(chart_value && chart_position){
        var analytics_value = JSON.parse( chart_value.value );
        if(analytics_value != ''){
           var chart_value_2 = Object.values(analytics_value);
        }
    
        var MONTHS = [langvars.January, langvars.February, langvars.March, langvars.April, langvars.May, langvars.June, langvars.July, langvars.August, langvars.September, langvars.October, langvars.November, langvars.December];
        var color = Chart.helpers.color;
        var barChartData2 = {
            labels: MONTHS,
            datasets: [{
                label: langvars.Services,
                backgroundColor: color('#4CAF50').alpha(0.5).rgbString(),
                borderColor: "#4CAF50",
                borderWidth: 1,
                data: chart_value_2
            }]
        };
    
        var ctx2 = chart_position.getContext('2d');
        window.myBar2 = new Chart(ctx2, {
            type: 'bar',
            data: barChartData2,
            options: {
                responsive: true,
                legend: {
                    display: false,
                    position: 'top',
                },
                title: {
                    display: true,
                    text: lbl_title
                }
            }
        });
    }
}

// Download Canvas in Image
function license_managment_download_canvas(id_button,id_canvas){
   var download = document.getElementById(id_button);
    var image = document.getElementById(id_canvas).toDataURL("image/png"); // .replace("image/png", "image/octet-stream");
    download.setAttribute("href", image);
    //download.setAttribute("download","archive.png");
/*
    var canvas = document.getElementById(id_canvas);
    var img    = canvas.toDataURL("image/png");
    document.write('<img src="'+img+'"/>');*/
/*
    var canvas = document.getElementById(id_canvas);
    var image = canvas.toDataURL("image/jpg");
  el.href = image;*/
}