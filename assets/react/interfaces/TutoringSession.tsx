import Building from "./Building";
import Tutor from "./Tutor";
import Tutoring from "./Tutoring";

export default interface TutoringSession {
    id: string,
    startDateTime: string,
    endDateTime: string,
    building: Building,
    room: string,
    isRemote: boolean,
    tutors: Tutor[],
    tutoring: Tutoring,
}
