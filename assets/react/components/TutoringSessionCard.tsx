import { useTranslation } from "react-i18next";
import React from "react";
import { Badge, Button } from "react-bootstrap";
import TutoringSession from "../interfaces/TutoringSession";
import { formatRoom, formatTutoringSessionDate } from "../utils";

export default function ({tutoringSession}) {
    const { t } = useTranslation();

    const formatTutors = (tutoringSession: TutoringSession): string[] => {
        return tutoringSession.tutors.map(function (tutor, index) {
            return tutor.firstName + ' ' + tutor.lastName + (tutoringSession.tutors[index + 1] ? ', ' : '');
        })
    }

    return <>
        <div className="card mb-3">
            <div className="card-body">
                <div className="row g-0">
                    <div className="col-md-10">
                        <div className="d-flex flex-column">
                            <div className="d-flex align-items-center">
                                <i className="fas fa-calendar-days text-primary px-2"></i>
                                <h6 className="tutoring-title bold mb-0">
                                    { formatTutoringSessionDate(tutoringSession)}
                                </h6>
                                { tutoringSession.isRemote ?
                                    <div>
                                        <i className="fas fa-video text-secondary px-2"></i>
                                        <a className="tutoring-session-description bold text-secondary">
                                            {t('tutee.access_tutoring_session')}
                                        </a>
                                    </div> :
                                    <div>
                                        <i className="fas fa-location-dot text-primary px-2"></i>
                                        <span className="tutoring-session-description bold">
                                            {t('tutor.default_room')} : { tutoringSession.room && tutoringSession.building ? formatRoom(tutoringSession) : t('utils.to_complete')}
                                        </span>
                                    </div>
                                }
                            </div>
                            <hr className="dotted mt-2 mb-1"/>
                            <div>
                                <Badge className="gray-badge">
                                    {tutoringSession.tutoring.name}
                                </Badge>
                                <span className="tutoring-session-description ms-2">
                                    <small>
                                        <span className="bold">{t('utils.tutors')} : </span>
                                        {formatTutors(tutoringSession)}
                                    </small>
                                </span>
                            </div>
                            <hr className="dotted mt-2 mb-1"/>
                            <div>
                                <span className="tutoring-session-description bold">
                                    <span className="bold">{t('utils.subjects')} : </span> en attente
                                </span>
                            </div>
                            { tutoringSession.isRemote ?
                                <div>
                                    <hr className="dotted mt-2 mb-1"/>
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
                    <div className="col-md-2 d-flex align-items-center justify-content-center">
                        <Button variant="secondary">
                            {t('tutee.register')}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </>;
}
