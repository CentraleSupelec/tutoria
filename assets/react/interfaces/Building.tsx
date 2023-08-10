import BasicInformation from "./BasicInformation";
import Campus from "./Campus";

export default interface Building extends BasicInformation {
    campus: Campus
}
