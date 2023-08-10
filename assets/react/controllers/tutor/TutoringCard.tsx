import React, { useEffect, useState } from 'react';
import FillTutoring from '../../components/FillTutoring';
import Routing from "../../../Routing";
import Tutoring from '../../interfaces/Tutoring';
import Campus from '../../interfaces/Campus';
import { useTranslation } from "react-i18next";
import Utils from "../../utils";

export default function ({tutoring}) {
    const { t } = useTranslation();
    const { displayRoom, displayDefaultDailySlot } = Utils();
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
            .then((data : Campus[]) => {
                setCampuses(data);
            })
    }

    if (!parsedTutoring) {
        return null;
    }

    return (
        <div className="card mb-3" key={`tutoring-${parsedTutoring.id}`}>
            <div className="card-body bg-tertiary">
                <div className="row g-0">
                    <div className="col-md-8">
                        <h6 className="tutoring-title">{parsedTutoring.name}</h6>
                        <div>
                            <div>
                                <i className="fas fa-calendar-days text-primary px-2"></i>
                                <span className="tutoring-description">
                                    {t('tutor.default_daily_slot')} : { parsedTutoring.defaultWeekDays ? displayDefaultDailySlot(parsedTutoring) : t('utils.to_complete')}
                                </span>
                            </div>
                            <div>
                                <i className="fas fa-location-dot text-primary px-2"></i>
                                <span className="tutoring-description">
                                    {t('tutor.default_room')} : { parsedTutoring.room && parsedTutoring.building ? displayRoom(parsedTutoring) : t('utils.to_complete')}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-4 d-flex align-items-center justify-content-end">
                        <div className="">
                            <FillTutoring tutoring={parsedTutoring} campuses={campuses}/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
