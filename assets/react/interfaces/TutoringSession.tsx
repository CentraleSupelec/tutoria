import Building from "./Building";

export default interface TutoringSession {
    startDateTime: string,
    endDateTime: string,
    building: Building,
    room: string,
    isRemote: boolean,
}
