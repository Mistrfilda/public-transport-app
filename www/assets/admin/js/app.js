import '../scss/app.scss';

import $ from 'jquery';
import 'bootstrap';

import naja from 'naja';
import netteForms from 'nette-forms';

netteForms.initOnLoad();
window.Nette = netteForms;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

import sbAdmin from '../sbadmin/js/sb-admin-2';

//Datagrid
import 'ublaboo-datagrid'
import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh';
import 'bootstrap-datepicker';
import 'bootstrap-select';

$(document).ready(function () {
    initCustomJs();
});

naja.snippetHandler.addEventListener('afterUpdate', (event) => {
    initCustomJs();
});

function initCustomJs() {
    $('.bootstrap-selectpicker').selectpicker({
        'liveSearch': true,
        'style': 'btn-primary'
    });
}