export interface MapObject {
    coordinates: {
        lat: number,
        lng: number
    };
    label: string;
    mapIcon: string;
    infoWindowLines: string[];
}