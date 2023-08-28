import BasicInformation from "./BasicInformation";
import Building from "./Building";
import Student from "./Student";
import Tutoring from "./Tutoring";

export default interface TutoringSession extends BasicInformation {
    id: string,
    building: Building,
    room: string,
    isRemote: boolean,
    onlineMeetingUri: string,
    startDateTime: Date
    endDateTime: Date
    tutors: Student[],
    tutoring: Tutoring,
    students: Student[],
}
