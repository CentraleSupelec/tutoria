import React, { useEffect, useState } from 'react';
import TutoringSession from "../../interfaces/TutoringSession";
import TutoringSessionsList from "../../components/TutoringSessionsList";
import { useTranslation } from "react-i18next";
import Routing from "../../../Routing";
import Tutoring from "../../interfaces/Tutoring";
import RegisteredTutoringSessions from "../../components/RegisteredTutoringSessions";

export default function ({tutoringSessions, tutorings, incomingTutoringSessions, pastTutoringSessions, userId}) {
    const { t } = useTranslation();
    const [parsedTutoringSessions, setParsedTutoringSessions] = useState<TutoringSession[]>([]);
    const [parsedTutorings, setParsedTutorings] = useState<Tutoring[]>([]);
    const [parsedIncomingTutoringSessions, setParsedIncomingTutoringSessions] = useState<TutoringSession[]>([]);
    const [parsedPastTutoringSessions, setParsedPastTutoringSessions] = useState<TutoringSession[]>([]);

    useEffect(() => {
        if (tutoringSessions) {
            setParsedTutoringSessions(JSON.parse(tutoringSessions));
        }
        if (tutorings) {
            setParsedTutorings(JSON.parse(tutorings));
        }
        if (incomingTutoringSessions) {
            setParsedIncomingTutoringSessions(JSON.parse(incomingTutoringSessions));
        }
        if (pastTutoringSessions) {
            setParsedPastTutoringSessions(JSON.parse(pastTutoringSessions));
        }
    }, [tutoringSessions, tutorings, incomingTutoringSessions, pastTutoringSessions]);

    const updateTutorings = (tutorings: string[]): void => {
        const params = new FormData();

        for (let i = 0; i < tutorings.length; i++) {
            params.append('tutoring_session_search[tutorings][]', tutorings[i]);
        }

        const requestOptions = {
            method: 'POST',
            body: params
        };

        fetch(Routing.generate('tutoring_sessions_by_tutorings'), requestOptions)
            .then((resp) => resp.json())
            .then((data : TutoringSession[]) => {
                setParsedTutoringSessions(data);
            })
    }

    const updateRegistration = (): void => {
        fetch(Routing.generate('get_incoming_tutoring_sessions'))
            .then((resp) => resp.json())
            .then((data: TutoringSession[]) => {
                setParsedIncomingTutoringSessions(data);
            })
        fetch(Routing.generate('get_past_tutoring_sessions'))
            .then((resp) => resp.json())
            .then((data: TutoringSession[]) => {
                setParsedPastTutoringSessions(data);
            })
        const filteredTutorings = JSON.parse(localStorage.getItem('tutoringFilter'))?? [];
        onFilterChange(filteredTutorings);
    };

    const onFilterChange = (tutorings: Tutoring[]) => {
        localStorage.setItem('tutoringFilter', JSON.stringify(tutorings));
        const tutoringIds = tutorings.map((tutoring) => {
            return tutoring.id;
        })
        updateTutorings(tutoringIds);
    }

    return <>
        <div className="card mb-3">
            <div className="card-header bg-primary text-white">
                <h4 className="mb-0">{t('tutee.my_registrations')}</h4>
            </div>
            <div className="card-body bg-tertiary">
                <RegisteredTutoringSessions
                    incomingTutoringSessions={parsedIncomingTutoringSessions}
                    pastTutoringSessions={parsedPastTutoringSessions}
                    userId={userId}
                    updateRegistration={updateRegistration}
                />
            </div>
        </div>
        <div className="card mb-3">
            <div className="card-header bg-primary text-white">
                <h4 className="mb-0">{t('tutee.upcoming_tutorings')}</h4>
            </div>
            <div className="card-body">
                <TutoringSessionsList
                    tutoringSessions={parsedTutoringSessions}
                    tutorings={parsedTutorings}
                    userId={userId}
                    updateRegistration={updateRegistration}
                    onFilterChange={onFilterChange}
                />
            </div>
        </div>
    </>
}
