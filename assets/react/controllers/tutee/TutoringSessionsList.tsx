import React, { useEffect, useState } from 'react';
import Tutoring from '../../interfaces/Tutoring';
import { useTranslation } from "react-i18next";
import TutoringSession from "../../interfaces/TutoringSession";
import TutoringSessionCard from "../../components/TutoringSessionCard";
import TutoringFilter from "../../components/TutoringFilter";
import Routing from "../../../Routing";

export default function ({tutoringSessions, tutorings}) {
    const [parsedTutoringSessions, setParsedTutoringSessions] = useState<TutoringSession[]>();
    const [parsedTutorings, setParsedTutorings] = useState<Tutoring[]>();

    useEffect(() => {
        if (tutoringSessions) {
            setParsedTutoringSessions(JSON.parse(tutoringSessions));
        }
        if (tutorings) {
            setParsedTutorings(JSON.parse(tutorings));
        }
    }, [tutoringSessions, tutorings]);

    const updateTutorings = (tutorings: Tutoring[]):void => {
        const requestOptions = {
            method: 'POST',
            body: JSON.stringify({ tutorings: tutorings })
        };

        fetch(Routing.generate('tutoring_sessions_by_tutorings'), requestOptions)
            .then((resp) => resp.json())
            .then((data : TutoringSession[]) => {
                setParsedTutoringSessions(data);
            })
    }

    return (
        <div>
            <TutoringFilter tutorings={parsedTutorings} updateTutorings={updateTutorings}></TutoringFilter>
            {parsedTutoringSessions? parsedTutoringSessions.map(
                tutoringSession => {
                    return(
                        <div key={`tutoring-${tutoringSession.id}`}>
                            <TutoringSessionCard tutoringSession={tutoringSession}></TutoringSessionCard>
                        </div>
                    )
                }
            ) : null
            }
        </div>
    )
}
