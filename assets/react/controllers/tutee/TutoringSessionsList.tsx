import React, { useEffect, useState } from 'react';
import Tutoring from '../../interfaces/Tutoring';
import TutoringSession from "../../interfaces/TutoringSession";
import TutoringSessionCard from "../../components/TutoringSessionCard";
import TutoringFilter from "../../components/TutoringFilter";
import Routing from "../../../Routing";

export default function ({tutoringSessions, tutorings}) {
    const [parsedTutoringSessions, setParsedTutoringSessions] = useState<TutoringSession[]>([]);
    const [parsedTutorings, setParsedTutorings] = useState<Tutoring[]>([]);

    useEffect(() => {
        if (tutoringSessions) {
            setParsedTutoringSessions(JSON.parse(tutoringSessions));
        }
        if (tutorings) {
            setParsedTutorings(JSON.parse(tutorings));
        }
    }, [tutoringSessions, tutorings]);

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

    return (
        <div>
            <TutoringFilter tutorings={parsedTutorings} updateTutorings={updateTutorings}></TutoringFilter>
            <ul className="list-unstyled">
                {parsedTutoringSessions? parsedTutoringSessions.map(
                    tutoringSession => {
                        return(
                            <li key={`tutoring-${tutoringSession.id}`}>
                                <TutoringSessionCard initialTutoringSession={tutoringSession} tutoring={tutoringSession.tutoring} campuses={null} isUserTutor={false}></TutoringSessionCard>
                            </li>
                        )
                    }
                ) : null
                }
            </ul>
        </div>
    )
}
