import BasicInformation from "./BasicInformation";
import Building from "./Building";

export default interface Campus extends BasicInformation {
    buildings: Building[],
}
