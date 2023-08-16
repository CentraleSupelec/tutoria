import BasicInformation from "./BasicInformation";
import Building from "./Building";
import Tutor from "./Tutor";
import Tutoring from "./Tutoring";

export default interface TutoringSession extends BasicInformation {
    id: string,
    building: Building,
    room: string,
    isRemote: boolean,
    onlineMeetingUri: string,
    startDateTime: Date
    endDateTime: Date
    tutors: Tutor[],
    tutoring: Tutoring,
}
