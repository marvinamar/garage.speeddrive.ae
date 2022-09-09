!(function (NioApp, $) {
    "use strict";

    var userGrowth = {
            labels: labels,
            dataUnit: currency,
            lineTension: .4,
            legend: !0,
            datasets: [{
                label: "Total",
                color: "#0fac81",
                background: NioApp.hexRGB('#0fac81',.085),
                data: amount
            }]
        };


    function lineChart(selector, set_data) {
        var $selector = $(selector || ".line-chart");
        $selector.each(function() {
            for (var $self = $(this), _self_id = $self.attr("id"), _get_data = void 0 === set_data ? eval(_self_id) : set_data, selectCanvas = document.getElementById(_self_id).getContext("2d"), chart_data = [], i = 0; i < _get_data.datasets.length; i++) chart_data.push({
                label: _get_data.datasets[i].label,
                tension: _get_data.lineTension,
                backgroundColor: _get_data.datasets[i].background,
                borderWidth: 2,
                borderColor: _get_data.datasets[i].color,
                pointBorderColor: _get_data.datasets[i].color,
                pointBackgroundColor: "#fff",
                pointHoverBackgroundColor: "#fff",
                pointHoverBorderColor: _get_data.datasets[i].color,
                pointBorderWidth: 2,
                pointHoverRadius: 4,
                pointHoverBorderWidth: 2,
                pointRadius: 4,
                pointHitRadius: 4,
                data: _get_data.datasets[i].data
            });
            var chart = new Chart(selectCanvas, {
                type: "line",
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data
                },
                options: {
                    legend: {
                        display: false,
                        labels: {
                            boxWidth: 12,
                            padding: 20,
                            fontColor: "#6783b8"
                        }
                    },
                    maintainAspectRatio: !1,
                    tooltips: {
                        enabled: !0,
                        callbacks: {
                            title: function(a, t) {
                                return t.labels[a[0].index]
                            },
                            label: function(a, t) {
                                return t.datasets[a.datasetIndex].data[a.index] + " " + _get_data.dataUnit
                            }
                        },
                        backgroundColor: "#eff6ff",
                        titleFontSize: 13,
                        titleFontColor: "#6783b8",
                        titleMarginBottom: 6,
                        bodyFontColor: "#9eaecf",
                        bodyFontSize: 12,
                        bodySpacing: 4,
                        yPadding: 10,
                        xPadding: 10,
                        footerMarginTop: 0,
                        displayColors: !1
                    },
                    scales: {
                        yAxes: [{
                            display: !0,
                            ticks: {
                                beginAtZero: !1,
                                fontSize: 12,
                                fontColor: "#9eaecf",
                                padding: 10
                            },
                            gridLines: {
                                color: "#e5ecf8",
                                tickMarkLength: 0,
                                zeroLineColor: "#e5ecf8"
                            }
                        }],
                        xAxes: [{
                            display: !0,
                            ticks: {
                                fontSize: 12,
                                fontColor: "#9eaecf",
                                source: "auto",
                                padding: 5
                            },
                            gridLines: {
                                color: "transparent",
                                tickMarkLength: 10,
                                zeroLineColor: "#e5ecf8",
                                offsetGridLines: !0
                            }
                        }]
                    }
                }
            })
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ lineChart(); });


    var taskStatistics = {
        labels : ["Completed", "In progress", "Cancelled"],
        dataUnit : 'Tasks',
        legend: false,
        datasets : [{
            borderColor : "#fff",
            background : ["#0fac81","#FFBB00","#f1f3f5"],
            data: tasksData
        }]
    };
    var projectStatistics = {
        labels : ["Completed", "In progress", "Booked In", "Cancelled"],
        dataUnit : 'Projects',
        legend: false,
        datasets : [{
            borderColor : "#fff",
            background : ["#0fac81","#FFBB00","#e85347","#f1f3f5"],
            data: projectData
        }]
    };

    function doughnutS1(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.statistics');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data;
            var selectCanvas = document.getElementById(_self_id).getContext("2d");

            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    backgroundColor: _get_data.datasets[i].background,
                    borderWidth:2,
                    borderColor: _get_data.datasets[i].borderColor,
                    hoverBorderColor: _get_data.datasets[i].borderColor,
                    data: _get_data.datasets[i].data,
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'doughnut',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    legend: {
                        display: (_get_data.legend) ? _get_data.legend : false,
                        labels: {
                            boxWidth:12,
                            padding:20,
                            fontColor: '#6783b8',
                        }
                    },
                    rotation: -1.5,
                    cutoutPercentage:70,
                    maintainAspectRatio: false,
                    tooltips: {
                        enabled: true,
                        callbacks: {
                            title: function(tooltipItem, data) {
                                return data['labels'][tooltipItem[0]['index']];
                            },
                            label: function(tooltipItem, data) {
                                return data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']] + ' ' + _get_data.dataUnit;
                            }
                        },
                        backgroundColor: '#1c2b46',
                        titleFontSize: 13,
                        titleFontColor: '#fff',
                        titleMarginBottom: 6,
                        bodyFontColor: '#fff',
                        bodyFontSize: 12,
                        bodySpacing:4,
                        yPadding: 10,
                        xPadding: 10,
                        footerMarginTop: 0,
                        displayColors: false
                    },
                }
            });
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ doughnutS1(); });  

})(NioApp, jQuery);