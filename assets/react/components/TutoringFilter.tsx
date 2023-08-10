import { useTranslation } from "react-i18next";
import React from "react";
import Tutoring from "../interfaces/Tutoring";
import Select from "react-select";
import makeAnimated from "react-select/animated";

export default function ({tutorings, updateTutorings}) {
    const { t } = useTranslation();
    const animatedComponents = makeAnimated();

    const onChange = (tutorings) => {
        const tutoringIds = tutorings.map((tutoring) => {
            return tutoring.id;
        })
        updateTutorings(tutoringIds);
    }

    return <>
        <Select
            className='mb-3'
            components={animatedComponents}
            isMulti
            options={tutorings}
            getOptionLabel={(tutoring: Tutoring) => {
                return tutoring.name;
            }}
            getOptionValue={(tutoring: Tutoring) => {
                return tutoring.id;
            }}
            placeholder={t('tutee.choose_tutoring_filter')}
            noOptionsMessage={() => { return t('tutee.no_tutorings'); }}
            onChange={onChange}
        />
    </>;
}
