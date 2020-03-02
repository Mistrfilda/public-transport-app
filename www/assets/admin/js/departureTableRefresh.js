/* global naja, $ */
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

        setInterval(function () {
            naja.makeRequest(
                'GET',
                refreshUrl,
                null,
                {}
            );
        }, 10000);
    }
}