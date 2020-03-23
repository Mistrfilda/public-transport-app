import naja from "naja";

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
    }

    createLineCharts() {
        $('.chart--line').each(function (index, element) {
            let graphElement = $(element);
            let graphData = this.fetchData(graphElement);

            graphData.then(function (response) {
                console.log(response);
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
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        tooltips: this.tooltipDefaults
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
                console.log(response);
                let myChart = new this.chart(graphElement, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: response.datasets.label,
                            data: response.datasets.data,
                            backgroundColor: this.defaultBackgroundColor,
                            // backgroundColor: [
                            //     'rgba(255, 99, 132, 0.2)',
                            //     'rgba(54, 162, 235, 0.2)',
                            //     'rgba(255, 206, 86, 0.2)',
                            //     'rgba(75, 192, 192, 0.2)',
                            //     'rgba(153, 102, 255, 0.2)',
                            //     'rgba(255, 159, 64, 0.2)'
                            // ],
                            // borderColor: [
                            //     'rgba(255, 99, 132, 1)',
                            //     'rgba(54, 162, 235, 1)',
                            //     'rgba(255, 206, 86, 1)',
                            //     'rgba(75, 192, 192, 1)',
                            //     'rgba(153, 102, 255, 1)',
                            //     'rgba(255, 159, 64, 1)'
                            // ],
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
                        tooltips: this.tooltipDefaults
                    }
                });

                this.removeGraphSpinner(graphElement.prop('id'));
            }.bind(this));

        }.bind(this));
    }

    fetchData(element) {
        console.log(element.attr('data-chart-method'));

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