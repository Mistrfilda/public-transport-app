import '../scss/app.scss';

import $ from 'jquery';
import 'bootstrap';

import naja from 'naja';
import netteForms from '../nette/live-form-validation';

netteForms.initOnLoad();
window.Nette = netteForms;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

import '../sbadmin/js/sb-admin-2';

//Datagrid
import 'ublaboo-datagrid'
import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh';
import 'bootstrap-datepicker';
import 'bootstrap-select';

//Custom js
import clock from "./clock";
import departureTableRefresh from './departureTableRefresh';

let najaDepartureTableHandler = new departureTableRefresh(naja, $);

$(document).ready(function () {
    initCustomJs();
    clock();
    najaDepartureTableHandler.bind();
});

naja.snippetHandler.addEventListener('afterUpdate', () => {
    initCustomJs();
});

function initCustomJs() {
    $('.bootstrap-selectpicker').selectpicker({
        'liveSearch': true,
        'style': 'btn-primary'
    });

    $('.toast').toast('show');
}