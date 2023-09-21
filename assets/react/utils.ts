import Tutoring from "./interfaces/Tutoring";
import TutoringSession from "./interfaces/TutoringSession";
import { format } from "date-fns";
import { enUS, fr } from "date-fns/esm/locale";
import Building from "./interfaces/Building";
import ModalErrorsInterface from "./interfaces/ModalErrorsInterface";
import ErrorInterface from "./interfaces/ErrorInterface";

export const formatDefaultDay = (tutoring: Tutoring, t): string[] => {
    return (tutoring.defaultWeekDays.map(function (day, index) {
        return t('utils.days.' + day) + (tutoring.defaultWeekDays[index + 1] ? ', ' : '');
    }));
}

export const formatDefaultHour = (tutoring: Tutoring): string => {
    const padTo2Digits = (num: number) => {
        return String(num).padStart(2, '0');
    }

    const startDate = new Date(tutoring.defaultStartTime);
    const endDate = new Date(tutoring.defaultEndTime);
    const startSlot = padTo2Digits(startDate.getHours()) + ':' + padTo2Digits(startDate.getMinutes());
    const endSlot = padTo2Digits(endDate.getHours()) + ':' + padTo2Digits(endDate.getMinutes());

    return startSlot + '-' + endSlot;
}

export const formatRoom = (room: string, building: Building): string => {
    return (room + ', ' + building.name + ', ' + building.campus.name);
}

const capitalize = (str: string) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

export const formatTutoringSessionDate = (tutoringSession: TutoringSession): string => {
    const dateLocales = { 'fr': fr, 'en': enUS };
    const startDate = new Date(tutoringSession.startDateTime);
    const endDate = new Date(tutoringSession.endDateTime);

    return (
        capitalize(
            format(startDate, 'eeee dd/MM - HH:mm',
                {locale: dateLocales[document.documentElement.lang]})
        ) + format(endDate, '-HH:mm')
    );
}

export const daysArrayToDaysSelection = (days: string[]): DaysSelection => {
    const daysSelection: DaysSelection = {
        monday: false,
        tuesday: false,
        wednesday: false,
        thursday: false,
        friday: false
    }

    days.forEach((dayName) => {
        daysSelection[dayName] = true;
    })

    return daysSelection;
}

export const parseErrors = (errors : ErrorInterface[]): ModalErrorsInterface => {
    let parsedErrors: ModalErrorsInterface = {
        generalErrors: []
    };

    // When propertyPath = 'data', it means the error is at the root of the entity
    // Else propertyPath = 'data.fieldName' and the error is on a certain field of the entity
    errors.forEach(error => {
        if (error.propertyPath === 'data') {
            parsedErrors = {...parsedErrors, generalErrors: [...parsedErrors.generalErrors, error.message]}
        } else {
            parsedErrors = {...parsedErrors, [error.propertyPath.replace('data.', '')]: error.message};
        }
    });
    return parsedErrors;
}
