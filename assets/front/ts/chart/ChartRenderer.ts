import {Naja, Payload} from "naja/dist/Naja";
import Chart, {ChartTooltipItem} from "chart.js";
import {ChartData} from "./ChartData";


export class ChartRenderer {
    naja: Naja;

    defaultBackgroundColor: string;

    tooltipDefaults: object;

    constructor(naja: Naja) {
        this.naja = naja;
        this.setDefaults();

        this.defaultBackgroundColor = '#007bff';
        this.tooltipDefaults = this.getTooltipDefaults();
    }

    setDefaults() {
        Chart.defaults.global.defaultFontFamily = 'inter';
        Chart.defaults.global.defaultFontColor = '#858796';
        Chart.defaults.global.defaultColor = '#007bff';
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

    async createLineChart(graphCanvasElement: HTMLCanvasElement, chartDataUrl: string): Promise<void> {
        let graphData = this.fetchData(chartDataUrl);

        graphData.then(function (response: ChartData) {
            let tooltipDefaults = this.getTooltipDefaults();
            tooltipDefaults.callbacks = {
                label: function (tooltipItem: ChartTooltipItem, chart: Chart) {
                    return tooltipItem.yLabel + ' ' + response.tooltipSuffix;
                }
            };

            let myChart = new Chart(graphCanvasElement, {
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
        }.bind(this));
    }

    async createBarCharts(graphCanvasElement: HTMLCanvasElement, chartDataUrl: string): Promise<void> {
        let graphData = this.fetchData(chartDataUrl);

        graphData.then(function (response: ChartData) {
            let tooltipDefaults = this.getTooltipDefaults();
            tooltipDefaults.callbacks = {
                label: function (tooltipItem: ChartTooltipItem, chart: Chart) {
                    return tooltipItem.yLabel + ' ' + response.tooltipSuffix;
                }
            };
            let myChart = new Chart(graphCanvasElement, {
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
        }.bind(this));
    }


    async createDoughnutCharts(graphCanvasElement: HTMLCanvasElement, chartDataUrl: string): Promise<void> {
        let graphData = this.fetchData(chartDataUrl);

        graphData.then(function (response: ChartData) {
            let myChart = new Chart(graphCanvasElement, {
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

            this.removeGraphSpinner(graphCanvasElement);
        }.bind(this));
    }

    fetchData(url: string): Promise<Payload> {
        return this.naja.makeRequest(
            'GET',
            url,
            null,
            {
                history: false,
                responseType: 'json',
                unique: false
            },
        );
    }

    removeGraphSpinner(element: HTMLElement) {
        console.log(element);
    }
}