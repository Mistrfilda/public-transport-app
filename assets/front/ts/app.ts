declare global {
    interface Window {
        frontMenu: any;
        loadMap: any;
        googleMapControl: any;
        handleModal: any;
    }
}

import "../scss/index.scss";

import 'alpinejs';

import naja from "naja/dist/index.esm";
document.addEventListener('DOMContentLoaded', () => naja.initialize());

import './alpine/AppAlpine';

import {LoadMapHandler} from "./alpine/Map/LoadMapHandler";

let loadMapHandler = new LoadMapHandler(naja);
loadMapHandler.initListener();