export default class chartRenderer {
    constructor(naja, chart, $) {
        this.chart = chart;
        this.$ = $;
        this.naja = naja;
        this.setDefaults();

        this.defaultBackgroundColor = '#007bff';
        this.tooltipDefaults = this.getTooltipDefaults();
    }

    setDefaults() {
        this.chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        this.chart.defaults.global.defaultFontColor = '#858796';
        this.chart.defaults.global.defaultColor = '#007bff';
    }

    getTooltipDefaults() {
        return {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        }
    }

    async bindGraphs() {
        this.createBarCharts();
        this.createLineCharts();
        this.createDoughnutCharts();
    }

    createLineCharts() {
        $('.chart--line').each(function (index, element) {
            let graphElement = $(element);
            let graphData = this.fetchData(graphElement);

            graphData.then(function (response) {
                let tooltipDefaults = this.getTooltipDefaults();
                tooltipDefaults.callbacks = {
                    label: function(tooltipItem, chart) {
                        return tooltipItem.yLabel + ' ' + response.tooltipSuffix;
                    }
                };

                let myChart = new this.chart(graphElement, {
                    type: 'line',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: response.datasets.label,
                            data: response.datasets.data,
                            borderWidth: 1,
                            fill: false,
                            backgroundColor: this.defaultBackgroundColor,
                            borderColor: this.defaultBackgroundColor,
                            lineTension: 0.1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        tooltips: tooltipDefaults
                    }
                });

                this.removeGraphSpinner(graphElement.prop('id'));
            }.bind(this));

        }.bind(this));
    }

    createBarCharts() {
        $('.chart--bar').each(function (index, element) {
            let graphElement = $(element);
            let graphData = this.fetchData(graphElement);

            graphData.then(function (response) {
                let tooltipDefaults = this.getTooltipDefaults();
                tooltipDefaults.callbacks = {
                    label: function(tooltipItem, chart) {
                        return tooltipItem.yLabel + ' ' + response.tooltipSuffix;
                    }
                };
                let myChart = new this.chart(graphElement, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: response.datasets.label,
                            data: response.datasets.data,
                            backgroundColor: response.datasets.backgroundColors,
                            borderColor: response.datasets.borderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        tooltips: tooltipDefaults
                    }
                });

                this.removeGraphSpinner(graphElement.prop('id'));
            }.bind(this));

        }.bind(this));
    }

    createDoughnutCharts() {
        $('.chart--doughnut').each(function (index, element) {
            let graphElement = $(element);
            let graphData = this.fetchData(graphElement);

            graphData.then(function (response) {
                let myChart = new this.chart(graphElement, {
                    type: 'doughnut',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: response.datasets.label,
                            data: response.datasets.data,
                            borderWidth: 1,
                            fill: false,
                            backgroundColor: response.datasets.backgroundColors,
                            borderColor: response.datasets.borderColors,
                            lineTension: 0.1
                        }]
                    },
                    options: {
                        tooltips: this.tooltipDefaults
                    }
                });

                this.removeGraphSpinner(graphElement.prop('id'));
            }.bind(this));

        }.bind(this));
    }

    fetchData(element) {
        return naja.makeRequest(
            'GET',
            element.attr('data-chart-method'),
            null,
            {
                history: false,
                responseType: 'json',
                unique: false
            },
        );
    }

    removeGraphSpinner(id) {
        $('#' + id + '--spinner').hide();
    }
}