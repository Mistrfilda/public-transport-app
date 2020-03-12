import '../scss/app.scss';

import $ from 'jquery';
import 'bootstrap';

import naja from 'naja';
import netteForms from '../nette/live-form-validation';

import '../sbadmin/js/sb-admin-2';

//Datagrid
import 'ublaboo-datagrid'
import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh';
import 'bootstrap-datepicker';
import 'bootstrap-select';

//Custom js
import clock from "./clock";
import departureTableRefresh from './departureTableRefresh';
import modalExtension from "./modalExtension";

let najaDepartureTableHandler = new departureTableRefresh(naja, $);

netteForms.initOnLoad();
window.Nette = netteForms;

naja.registerExtension(modalExtension, $);
document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

$(document).ready(function () {
    initCustomJs();
    clock();
    najaDepartureTableHandler.placeListener();
});

naja.snippetHandler.addEventListener('afterUpdate', function () {
    initCustomJs();
});

function initCustomJs() {
    $('.bootstrap-selectpicker').selectpicker({
        'liveSearch': true,
        'style': 'btn-primary'
    });
}