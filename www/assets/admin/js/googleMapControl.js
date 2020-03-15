/* global naja, markerCluster */
export default class googleMap {
    constructor(naja, markerCluster) {
        this.naja = naja;
        this.markerCluster = markerCluster;

        this.mapElement = document.getElementById("map-control");

        this.callback = 'googleMapControl.initMap';
        window.googleMapControl = this;
        window.googleMapControl.initMap = this.initMap.bind(this);
    }

    initMap() {
        let map = new google.maps.Map(this.mapElement, {
            zoom: 3,
            center: {lat: 50.05972, lng: 14.40943}
        });

        let mapControl = this;
        naja.makeRequest(
            'GET',
            this.mapElement.dataset.mapObjectsMethod,
            null,
            {
                history: false,
                responseType: 'json',
                async: false
            },
        ).then(function (response) {
            mapControl.initMarkers(response, map);
        }).catch(function (error) {
            console.log(error);
            console.log('Map objects request failed');
        }).send();
    }

    initMarkers(response, map) {
        let markers = response.map(function (mapObject, i) {
            return new google.maps.Marker({
                position: mapObject.coordinates,
                label: mapObject.label
            });
        });

        let markerCluster = new this.markerCluster(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
    }

    load() {
        if (this.mapElement === null || this.mapElement.dataset.mapObjectsMethod === undefined || this.mapElement.dataset.mapApikey === undefined) {
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key='+ this.mapElement.dataset.mapApikey +'&callback='  + this.callback;
        script.async = true;
        document.body.append(script);
    }
}