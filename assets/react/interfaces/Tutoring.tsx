import BasicInformation from "./BasicInformation";
import Building from "./Building";
import Tutor from "./Tutor";
import TutoringSession from "./TutoringSession";

export default interface Tutoring extends BasicInformation {
    tutors: Tutor[],
    defaultBuilding: Building,
    defaultRoom: string,
    defaultStartTime: Date
    defaultEndTime: Date,
    tutoringSessions: TutoringSession[],
    defaultWeekDays: string[],
}
