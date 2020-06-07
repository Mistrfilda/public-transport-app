import '../scss/app.scss';

import $ from 'jquery';
import 'bootstrap';

import naja from 'naja';
import {LiveForm, Nette} from '../nette/live-form-validation';

import '../sbadmin/js/sb-admin-2';

//Datagrid
import 'ublaboo-datagrid'
import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh';
import 'bootstrap-datepicker';
import 'bootstrap-select';


//Google maps
import markerCluster from '@google/markerclustererplus';

//Charts
import chart from 'chart.js';

//Custom js
import clock from "./clock";
import departureTableRefresh from './departureTableRefresh';
import modalExtension from "./modalExtension";
import googleMap from './googleMapControl';
import chartRenderer from "./chartRenderer";

let najaDepartureTableHandler = new departureTableRefresh(naja, $);
let googleMapControl = new googleMap(naja, markerCluster);
let chartRendererControl = new chartRenderer(naja, chart, $);

LiveForm.setOptions({
    messageErrorPrefix: "12312"
});

Nette.initOnLoad();
window.Nette = Nette;
window.LiveForm = LiveForm;

naja.registerExtension(modalExtension, $);
document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

$(document).ready(function () {
    initCustomJs();
    clock();
    najaDepartureTableHandler.placeListener();
    googleMapControl.load();
    chartRendererControl.bindGraphs();
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