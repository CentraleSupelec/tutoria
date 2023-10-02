import { useTranslation } from "react-i18next";
import React, { useCallback, useEffect, useState } from "react";
import { Badge, Button } from "react-bootstrap";
import TutoringSession from "../interfaces/TutoringSession";
import { formatTutoringSessionDate, formatRoom } from "../utils";
import Tutoring from "../interfaces/Tutoring";
import Campus from "../interfaces/Campus";
import Routing from "../../Routing";
import EditTutoringSession from "./Modal/EditTutoringSession";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import DeleteConfirmation from "./Modal/DeleteConfirmation";
import { toast } from "react-toastify";

interface TutoringSessionCardProps {
    tutoring: Tutoring,
    initialTutoringSession: TutoringSession,
    campuses: Campus[],
    isUserTutor: boolean,
    userId?: string,
    onDelete?: Function,
    onUpdate?: Function,
}

export default function ({ initialTutoringSession, tutoring, campuses, isUserTutor, userId, onDelete, onUpdate }: TutoringSessionCardProps) {
    const { t } = useTranslation();

    const [tutoringSession, setTutoringSession] = useState<TutoringSession>();
    const now = new Date();

    const isTutoringSessionsEnded = (tutoringSession: TutoringSession) => now > new Date(tutoringSession.endDateTime);

    const formatTutors = (tutoringSession: TutoringSession): string[] => {
        return tutoringSession.tutors.map(function (tutor, index) {
            return tutor.firstName + ' ' + tutor.lastName + (tutoringSession.tutors[index + 1] ? ', ' : '');
        })
    }

    const fetchTutoringSession = async (): Promise<TutoringSession> => {
        const res = await fetch(Routing.generate('get_tutoring_session', {id: tutoringSession?.id}));
        if (!res.ok) {
            const error = `Failed to fetch tutoring session with status: ${res.status} - ${res.statusText}`;
            throw new Error(error);
        }
        const data = await res.json();
        setTutoringSession(data);
        return data;
    }

    const subscribe = async (): Promise<void> => {
        const res = await fetch(Routing.generate('subscribe_to_tutoring_session', { id: tutoringSession?.id }));
        if (!res.ok) {
            toast.error(t('tutee.register_failed'));
            return;
        }
        try {
            await onUpdate();
            await fetchTutoringSession();
            toast.success(t('tutee.register_succeeded'));
        } catch {
            toast.warning(t('tutee.update_failed'));
        }
    }

    const unsubscribe = async (): Promise<void> => {
        const res = await fetch(Routing.generate('unsubscribe_to_tutoring_session', { id: tutoringSession?.id }));
        if (!res.ok) {
            toast.error(t('tutee.unregister_failed'));
            return;
        }
        try {
            await onUpdate();
            await fetchTutoringSession();
            toast.success(t('tutee.unregister_succeeded'));
        } catch {
            toast.warning(t('tutee.update_failed'));
        }
    }

    const deleteTutoringSession = () => {
        fetch(Routing.generate('delete_tutoring_session', { id: tutoringSession?.id }), {
            method: 'POST'
        })
            .then(() => {
                onDelete();
            })
    }

    const userInListOfTutoringSessionStudent = useCallback(() => {
        return !!tutoringSession.students.find(student => student.id === userId);
    }, [tutoringSession, userId]);

    useEffect(() => {
        if (initialTutoringSession) {
            setTutoringSession(initialTutoringSession);
        }
    }, [initialTutoringSession]);

    return <>
        {tutoringSession ?
            <div className="card mb-3">
                <div className="card-body">
                    <div className="row g-0">
                        <div className="col-md-10">
                            <div className="d-flex flex-column">
                                <div className="d-flex align-items-center">
                                    <i className="fas fa-calendar-days text-primary px-2"></i>
                                    <h6 className="tutoring-title bold mb-0">
                                        {formatTutoringSessionDate(tutoringSession)}
                                    </h6>
                                    {tutoringSession.isRemote ?
                                        <div>
                                            <a className="text-secondary d-flex align-items-center" href={tutoringSession.onlineMeetingUri} target="_blank">
                                                <FontAwesomeIcon className="text-secondary px-2" icon="video" />
                                                <span className="tutoring-session-description bold">
                                                    {t('tutee.access_tutoring_session')}
                                                </span>
                                            </a>
                                        </div> :
                                        <div className="d-flex align-items-center">
                                            <FontAwesomeIcon className="text-primary px-2" icon="location-dot" />
                                            <span className="tutoring-session-description bold">
                                                {t('tutor.default_room')} : {tutoringSession.room && tutoringSession.building ? formatRoom(tutoringSession.room, tutoringSession.building) : t('utils.to_complete')}
                                            </span>
                                        </div>
                                    }
                                </div>
                                <hr className="dotted mt-2 mb-1" />
                                <div>
                                    <Badge className="gray-badge">
                                        {tutoring.name}
                                    </Badge>
                                    <span className="tutoring-session-description ms-2">
                                        <small>
                                            <span className="bold">{t('utils.tutors')} : </span>
                                            {formatTutors(tutoringSession)}
                                        </small>
                                    </span>
                                </div>
                                <hr className="dotted mt-2 mb-1" />
                                <div>
                                    <span className="tutoring-session-description bold">
                                        <span className="bold">{t('utils.subjects')} : </span> en attente
                                    </span>
                                </div>
                                {tutoringSession.isRemote ?
                                    <div>
                                        <hr className="dotted mt-2 mb-1" />
                                        <div className="d-flex align-items-center">
                                            <i className="fas fa-comment text-primary px-2"></i>
                                            <Badge className="gray-badge multi-line-badge">
                                                <small>{t('tutee.info_remote')}</small>
                                            </Badge>
                                        </div>
                                    </div>
                                    : null
                                }
                            </div>
                        </div>
                        <div className="col-md-2 d-flex align-items-center justify-content-around ps-3">
                            {isUserTutor?
                            <>
                                <EditTutoringSession tutoring={tutoring} tutoringSession={tutoringSession} campuses={campuses} updateTutoringSession={fetchTutoringSession} isTutoringSessionsEnded={isTutoringSessionsEnded(tutoringSession)} />
                                <DeleteConfirmation onConfirmDelete={deleteTutoringSession} isTutoringSessionsEnded={isTutoringSessionsEnded(tutoringSession)} />
                            </>
                                :
                                (userInListOfTutoringSessionStudent()?
                                    <Button variant="secondary" onClick={unsubscribe}>
                                        {t('tutee.unregister')}
                                    </Button>
                                    :
                                    <Button variant="secondary" onClick={subscribe}>
                                        {t('tutee.register')}
                                    </Button>
                                )
                            }
                        </div>
                    </div>
                </div>
            </div>
            : <></>
        }
    </>;
}
