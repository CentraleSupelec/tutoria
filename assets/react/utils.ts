import Tutoring from "./interfaces/Tutoring";
import TutoringSession from "./interfaces/TutoringSession";
import { format } from "date-fns";
import { enUS, fr } from "date-fns/esm/locale";

export const formatDefaultDay = (tutoring: Tutoring, t): string[] => {
    return (tutoring.defaultWeekDays.map(function (day, index) {
        return t('utils.days.' + day) + (tutoring.defaultWeekDays[index + 1] ? ', ' : '');
    }));
}

export const formatDefaultHour = (tutoring: Tutoring): string => {
    const padTo2Digits = (num) => {
        return String(num).padStart(2, '0');
    }

    const startDate = new Date(tutoring.defaultStartTime);
    const endDate = new Date(tutoring.defaultEndTime);
    const startSlot = padTo2Digits(startDate.getHours()) + ':' + padTo2Digits(startDate.getMinutes());
    const endSlot = padTo2Digits(endDate.getHours()) + ':' + padTo2Digits(endDate.getMinutes());

    return startSlot + '-' + endSlot;
}

export const formatRoom = (tutoring: Tutoring): string => {
    return (tutoring.room + ', ' + tutoring.building.name + ', ' + tutoring.building.campus.name);
}

const capitalize = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

export const displayTutoringSessionDate = (tutoringSession: TutoringSession) => {
    const dateLocales = { 'fr-FR': fr, 'en-Us': enUS };
    const browserLocale = navigator.language;
    const startDate = new Date(tutoringSession.startDateTime);
    const endDate = new Date(tutoringSession.endDateTime);

    return (
        capitalize(
            format(startDate, 'eeee dd/MM - HH:mm',
                {locale: dateLocales[browserLocale]})
        ) + format(endDate, '-HH:mm')
    );
}
