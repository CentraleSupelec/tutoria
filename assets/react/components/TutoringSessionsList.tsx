import React from 'react';
import TutoringSessionCard from "./TutoringSessionCard";
import TutoringFilter from "./TutoringFilter";

export default function ({tutoringSessions, tutorings, userId, updateRegistration, onFilterChange}) {
    return (
        <div>
            <TutoringFilter tutorings={tutorings} onFilterChange={onFilterChange}></TutoringFilter>
            <ul className="list-unstyled">
                {tutoringSessions? tutoringSessions
                    .map(tutoringSession => {
                        return (
                            <li key={`tutoring-${tutoringSession.id}`}>
                                <TutoringSessionCard
                                    initialTutoringSession={tutoringSession}
                                    tutoring={tutoringSession.tutoring}
                                    campuses={null}
                                    isUserTutor={false}
                                    userId={userId}
                                    onUpdate={updateRegistration}
                                />
                            </li>
                        )
                    }) : null
                }
            </ul>
        </div>
    )
}
