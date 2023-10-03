import React, { useEffect, useState } from 'react';
import Routing from "../../../Routing";
import Tutoring from '../../interfaces/Tutoring';
import Campus from '../../interfaces/Campus';
import { useTranslation } from "react-i18next";
import { formatDefaultDay, formatDefaultHour, formatRoom } from "../../utils";
import TutoringSessionCreationModal from '../../components/Modal/TutoringSessionCreationModal';
import EditTutoring from '../../components/Modal/EditTutoring';
import TutoringSessionCard from '../../components/TutoringSessionCard';

export default function ({ tutoring }) {
    const { t } = useTranslation();
    const [parsedTutoring, setParsedTutoring] = useState<Tutoring>();
    const [campuses, setCampuses] = useState<Campus[]>();

    useEffect(() => {
        if (tutoring) {
            setParsedTutoring(JSON.parse(tutoring));
        }
        fetchCampuses();
    }, [tutoring]);

    const fetchCampuses = () => {
        fetch(Routing.generate('campuses'))
            .then((resp) => resp.json())
            .then((data: Campus[]) => {
                setCampuses(data);
            })
    }

    const fetchTutoring = () => {
        fetch(Routing.generate('get_tutoring', { id: parsedTutoring.id }))
            .then((resp) => resp.json())
            .then((data: Tutoring) => {
                setParsedTutoring(data);
            })
    }

    if (!parsedTutoring) {
        return null;
    }

    return (
        <div>
            <div className="card mb-3" key={`tutoring-${parsedTutoring.id}`}>
                <div className="card-body bg-tertiary">
                    <div className="row g-0">
                        <div className="col-md-8">
                            <h6 className="tutoring-title bold">{parsedTutoring.name}</h6>
                            <div>
                                <i className="fas fa-calendar-days text-primary px-2"></i>
                                <span className="tutoring-description">
                                    {t('tutor.default_day')} : {parsedTutoring.defaultWeekDays ? formatDefaultDay(parsedTutoring, t) : t('utils.to_complete')}
                                </span>
                            </div>
                            <div>
                                <i className="fas fa-clock text-primary px-2"></i>
                                <span className="tutoring-description">
                                    {t('tutor.default_hour')} : { parsedTutoring.defaultStartTime ? formatDefaultHour(parsedTutoring) : t('utils.to_complete')}
                                </span>
                            </div>
                            <div>
                                <i className="fas fa-location-dot text-primary px-2"></i>
                                <span className="tutoring-description">
                                    {t('tutor.default_room')} : { parsedTutoring.defaultRoom && parsedTutoring.defaultBuilding ? formatRoom(parsedTutoring.defaultRoom, parsedTutoring.defaultBuilding) : t('utils.to_complete')}
                                </span>
                            </div>
                        </div>
                        <div className="col-md-4 d-flex align-items-center justify-content-end">
                            <div>
                                {parsedTutoring.tutoringSessions.length === 0 ?
                                    <EditTutoring tutoring={parsedTutoring} campuses={campuses} onUpdate={fetchTutoring} withBatchTutoringSessionCreation={true} />
                                    :
                                    <div className='d-flex'>
                                        <div className='pe-3'>
                                            <TutoringSessionCreationModal tutoring={parsedTutoring} campuses={campuses} onUpdate={fetchTutoring} />
                                        </div>
                                        <div>
                                            <EditTutoring tutoring={parsedTutoring} campuses={campuses} onUpdate={fetchTutoring} withBatchTutoringSessionCreation={false} />
                                        </div>
                                    </div>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {parsedTutoring.tutoringSessions ?
                <div className='d-flex flex-column ps-5'>
                    {parsedTutoring.tutoringSessions
                        .map(tutoringSession =>
                            <TutoringSessionCard
                                key={`tutoring-session-${tutoringSession.id}`}
                                tutoring={parsedTutoring}
                                initialTutoringSession={tutoringSession}
                                campuses={campuses}
                                isUserTutor={true}
                                onDelete={fetchTutoring}
                            />
                    )}
                </div>
                : null
            }
        </div>
    )
}
