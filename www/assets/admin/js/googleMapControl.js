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
            zoom: 8,
            center: {lat: 50.05972, lng: 14.40943},
            styles: this.getMapStyles()
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
            let marker = new google.maps.Marker({
                position: mapObject.coordinates,
                label: {
                    text: mapObject.label,
                    color: "white",
                    fontSize: "10 px"
                },
                icon: {
                    url: "https://maps.google.com/mapfiles/kml/shapes/bus.png",
                    scaledSize: new google.maps.Size(30, 30),
                }
            });

            let contentString = '<ul class="list-group app-map-infowindow">\n';

            let index = 0;
            mapObject.infoWindowLines.forEach(function (line) {
                if (index % 2 === 0) {
                    contentString = contentString +  '<li class="list-group-item active">' + line + '</li>\n';
                } else {
                    contentString = contentString +  '<li class="list-group-item">' + line + '</li>\n';
                }
                index = index + 1;
            });

            contentString = contentString + '</ul>';

            let infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            marker.addListener('click', function() {
                infowindow.open(map, marker);
            });

            return marker;
        });

        let markerCluster = new this.markerCluster(map, markers,
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

    getMapStyles() {
        return [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#242f3e"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#746855"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#242f3e"
                    }
                ]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [
                    {
                        "color": "#d59563",
                    },
                    {
                        "visibility": 'off'
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#263c3f"
                    },
                    {
                        "visibility": 'off'
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#6b9a76"
                    },
                    {
                        "visibility": 'off'
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#38414e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#212a37"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9ca5b3"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#746855"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#1f2835"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#f3d19c"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#2f3948"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#17263c"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#515c6d"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#17263c"
                    }
                ]
            }
        ];
    }
}