import { useTranslation } from "react-i18next";
import React from "react";
import Tutoring from "../interfaces/Tutoring";
import Select from "react-select";
import makeAnimated from "react-select/animated";

interface TutoringFilterProps {
    tutorings: Tutoring[],
    onFilterChange: (tutorings: string[]) => void,
}

export default function ({tutorings, onFilterChange}: TutoringFilterProps) {
    const { t } = useTranslation();
    const animatedComponents = makeAnimated();

    const defaultValues = () => {
        const defaultTutorings = JSON.parse(localStorage.getItem('tutoringFilter'))?? [];
        onFilterChange(defaultTutorings);

        return defaultTutorings;
    }

    return <>
        <div className="card mb-3 bg-secondary">
            <div className="card-body">
                <h6 className="tutoring-title bold text-white">
                    {t('tutee.tutoring_filter')}
                </h6>
                <Select
                    className='mb-3'
                    components={animatedComponents}
                    isMulti
                    options={tutorings}
                    defaultValue={defaultValues}
                    getOptionLabel={(tutoring: Tutoring) => {
                        return tutoring.name;
                    }}
                    getOptionValue={(tutoring: Tutoring) => {
                        return tutoring.id;
                    }}
                    placeholder={t('tutee.choose_tutoring_filter')}
                    noOptionsMessage={() => { return t('tutee.no_tutorings'); }}
                    onChange={onFilterChange}
                />
            </div>
        </div>
    </>;
}
