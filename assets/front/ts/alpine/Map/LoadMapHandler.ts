import {Naja} from "naja/dist/Naja";
import {MapControl} from "../../map/MapControl";

export class LoadMapHandler {
    private naja: Naja;

    private mapControl: MapControl;

    public constructor(naja: Naja) {
        this.naja = naja;
        this.mapControl = new MapControl(naja);
    }

    public initListener() {
        const map = this.mapControl;
        window.loadMap = function(): object {
            return {
                show: true,
                showMap() {
                    map.load();
                }
            }
        }
    }
}