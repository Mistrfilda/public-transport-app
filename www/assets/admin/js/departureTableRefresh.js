/* global naja, $, intervalFunction */
export default class departureTableRefresh {
    constructor(naja, $) {
        this.naja = naja;
        this.$ = $;
    }

    bind() {
        let departureTable = $('[naja-departure-table-id]');
        let refreshUrl = departureTable.attr('naja-departure-table-url');

        if (departureTable.length === 0 || refreshUrl === 'undefined') {
            return;
        }

        this.intervalFunction = setInterval(function () {
            naja.makeRequest(
                'GET',
                refreshUrl,
                null,
                {
                    history: false
                }
            );
        }, 10000);
    }

    stop() {
        clearInterval(this.intervalFunction);
    }
}