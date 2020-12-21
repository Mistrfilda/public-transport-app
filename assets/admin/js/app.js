import '../scss/app.scss';

import $ from 'jquery';
import 'bootstrap';

import naja from 'naja';
import './customLiveFormValidation';

import '../sbadmin/js/sb-admin-2';

//Datagrid
import {initDatagrid, instantUrlRefresh} from './updatedDatagrid';
import 'bootstrap-datepicker';
import 'bootstrap-select';


//Google maps
import markerCluster from '@google/markerclustererplus';

//Charts
import chart from 'chart.js';

//Custom js
import clock from "./clock";
import departureTableRefresh from './departureTableRefresh';
import ModalExtension from "./modalExtension";
import googleMap from './googleMapControl';
import chartRenderer from "./chartRenderer";

let najaDepartureTableHandler = new departureTableRefresh(naja, $);
let googleMapControl = new googleMap(naja, markerCluster);
let chartRendererControl = new chartRenderer(naja, chart, $);

naja.registerExtension(new ModalExtension());
initDatagrid(naja);
instantUrlRefresh(naja);
document.addEventListener('DOMContentLoaded', () => {
    naja.initialize()
});

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