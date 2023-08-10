import React, { useEffect, useState } from 'react';
import FillTutoring from '../components/FillTutoring';
import Routing from "../../Routing";
import Tutoring from '../interfaces/Tutoring';
import Campus from '../interfaces/Campus';
import { Card } from 'react-bootstrap';

export default function ({tutorings}) {
    const [parsedTutorings, setParsedTutorings] = useState<Tutoring[]>([]);
    const [campuses, setCampuses] = useState<Campus[]>();

    const fetchCampuses = () => {
        fetch(Routing.generate('campuses'))
            .then((resp) => resp.json())
            .then((data : Campus[]) => {
                setCampuses(data);
            })
    }

    useEffect(() => {
        if (tutorings) {
            setParsedTutorings(JSON.parse(tutorings));
        }
        fetchCampuses();
    }, [tutorings]);

    return (
        <Card>
            <ul className='m-2 p-2'>
                {parsedTutorings? parsedTutorings.map(
                    tutoring => {
                        return (
                            <li key={`tutorin-${tutoring.id}`} className='d-flex justify-content-between align-items-center py-1'>
                                <div className="list-inline-item">
                                    {tutoring.name}
                                </div>
                                <div className='list-inline-item'>
                                    <FillTutoring tutoring={tutoring} campuses={campuses}/>
                                </div>
                            </li>
                        )
                    }
                ) : null
                }
            </ul>
        </Card>
    )
}
