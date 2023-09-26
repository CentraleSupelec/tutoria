import React from 'react';
import TutoringSessionCard from "./TutoringSessionCard";
import {Tab, Tabs} from "react-bootstrap";
import {useTranslation} from "react-i18next";

export default function ({incomingTutoringSessions, pastTutoringSessions, userId, updateRegistration}) {
    const { t } = useTranslation();

    return <>
        <div className="row row-cols-1 registration-navbar">
            <Tabs
                defaultActiveKey="incoming"
                id="uncontrolled-tab-example"
                className="mb-3"
            >
                <Tab eventKey="incoming" title={t("tutee.incoming")}>
                    <ul className="list-unstyled">
                        {incomingTutoringSessions? incomingTutoringSessions
                            .map(incomingTutoringSession => {
                                return (
                                    <li key={`incoming-tutoring-${incomingTutoringSession.id}`}>
                                        <TutoringSessionCard
                                            initialTutoringSession={incomingTutoringSession}
                                            tutoring={incomingTutoringSession.tutoring}
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
                </Tab>
                <Tab eventKey="past" title={t("tutee.past")}>
                    <ul className="list-unstyled">
                        {pastTutoringSessions? pastTutoringSessions
                            .map(pastTutoringSession => {
                                return (
                                    <li key={`past-tutoring-${pastTutoringSession.id}`}>
                                        <TutoringSessionCard
                                            initialTutoringSession={pastTutoringSession}
                                            tutoring={pastTutoringSession.tutoring}
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
                </Tab>
            </Tabs>
        </div>
    </>
}
