import {Naja, Payload} from "naja/dist/Naja";
import {} from 'googlemaps';
import {getMapStyles} from "./MapStyle";
import {MapObject} from "./MapObject";
import markerClusterPlus from '@google/markerclustererplus';
import {getMainInfoWindowHtml, getDarkInfoWindowLine, getLightInfoWindowLine} from "./MapInfoWindow";

export class MapControl {
    naja: Naja;

    mapElement: HTMLElement;

    callback: string;

    public constructor(naja: Naja) {
        this.naja = naja;
        this.mapElement = document.getElementById("map-control");
        this.callback = 'googleMapControl.initMap';
        window.googleMapControl = this;
        window.googleMapControl.initMap = this.initMap.bind(this);
    }

    initMap() {
        let map = new google.maps.Map(this.mapElement, {
            zoom: 8,
            center: {lat: 50.05972, lng: 14.40943},
            styles: getMapStyles()
        });

        let mapControl = this;
        this.naja.makeRequest(
            'GET',
            this.mapElement.dataset.mapObjectsMethod,
            null,
            {
                history: false,
                responseType: 'json',
                async: false
            },
        ).then(function (response: Payload) {
            mapControl.initMarkers(response, map);
        }).catch(function (error) {
            console.log(error);
            console.log('Map objects request failed');
        });
    }

    initMarkers(response: Payload, map: google.maps.Map) {
        let markers = response.map(function (mapObject: MapObject, i: number) {

            let marker = new google.maps.Marker({
                position: mapObject.coordinates,
                label: {
                    text: mapObject.label,
                    color: "white",
                    fontSize: "10 px"
                },
                icon: {
                    url: mapObject.mapIcon,
                    scaledSize: new google.maps.Size(30, 30),
                }
            });

            let contentString = '';

            let index = 0;
            mapObject.infoWindowLines.forEach(function (line: string) {
                if (index % 2 === 0) {
                    contentString = contentString + getDarkInfoWindowLine(line);
                } else {
                    contentString = contentString +  getLightInfoWindowLine(line);
                }
                index = index + 1;
            });

            let infowindow = new google.maps.InfoWindow({
                content: getMainInfoWindowHtml(contentString)
            });

            marker.addListener('click', function() {
                infowindow.open(map, marker);
            });

            return marker;
        });

        let markerCluster = new markerClusterPlus(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
    }

    async load() {
        if (this.mapElement === null || this.mapElement.dataset.mapObjectsMethod === undefined || this.mapElement.dataset.mapApikey === undefined) {
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key='+ this.mapElement.dataset.mapApikey +'&callback='  + this.callback;
        script.async = true;
        document.body.append(script);
    }
}