import BasicInformation from "./BasicInformation";
import Building from "./Building";
import Tutor from "./Tutor";

export default interface Tutoring extends BasicInformation {
    tutors: Tutor[],
    building: Building,
    room: string,
}
