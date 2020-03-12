/* global naja, $, intervalFunction */
export default class departureTableRefresh {
    constructor(naja, $) {
        naja.addEventListener('complete', this.openModal.bind(this));
    }

    openModal({xhr, response, options}) {
        let modalId = response.modalId;
        let showModal = response.showModal;

        if (showModal === undefined || showModal === false) {
            return;
        }

        $("#" + modalId).modal('show');
    }
}