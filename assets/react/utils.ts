import Tutoring from "./interfaces/Tutoring";
import {useTranslation} from "react-i18next";

export default function Utils() {
    const { t } = useTranslation();

    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    const padTo2Digits = (num) => {
        return String(num).padStart(2, '0');
    }

    const displayTutoringTimeSlot = (tutoring: Tutoring) => {
        const startDate = new Date(tutoring.startTime);
        const endDate = new Date(tutoring.endTime);
        const startSlot = padTo2Digits(startDate.getHours()) + ':' + padTo2Digits(startDate.getMinutes());
        const endSlot = padTo2Digits(endDate.getHours()) + ':' + padTo2Digits(endDate.getMinutes());

        return startSlot + '-' + endSlot;
    }

    const displayDefaultDailySlot = (tutoring: Tutoring) => {
        return (tutoring.defaultWeekDays.map(function (dayNumber, index) {
            let day = t('utils.days.' + days[dayNumber]);

            return day + ' ' + displayTutoringTimeSlot(tutoring) + (tutoring.defaultWeekDays[index + 1] ? ' ; ' : '');
        }));
    }

    const displayRoom = (tutoring: Tutoring) => {
        return (tutoring.room + ', ' + tutoring.building.name + ', ' + tutoring.building.campus.name);
    }

    return { displayRoom, displayDefaultDailySlot };
}
