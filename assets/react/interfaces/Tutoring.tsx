import BasicInformation from "./BasicInformation";
import Building from "./Building";
import Tutor from "./Tutor";
import TutoringSession from "./TutoringSession";

export default interface Tutoring extends BasicInformation {
    tutors: Tutor[],
    building: Building,
    room: string,
    defaultStartTime: string,
    defaultEndTime: string,
    defaultWeekDays: [],
    tutoringSessions: TutoringSession[],
}
