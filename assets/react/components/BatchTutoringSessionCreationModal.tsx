import React, { ChangeEvent, forwardRef, MutableRefObject, useEffect, useImperativeHandle, useState } from 'react';
import { Badge, Form} from 'react-bootstrap';
import DatePicker from 'react-date-picker';
import TimePicker from 'react-time-picker';
import Routing from "../../Routing";
import Building from '../interfaces/Building';
import Campus from '../interfaces/Campus';
import Tutoring from '../interfaces/Tutoring';
import Ref from '../interfaces/Ref';
interface BatchTutoringSessionCreationModalProps {
    tutoring: Tutoring,
    campuses: Campus[],
    toggleModal: Function,
}

const BatchTutoringSessionCreationModal = forwardRef(({tutoring, campuses, toggleModal}: BatchTutoringSessionCreationModalProps, ref: MutableRefObject<Ref>) => {
    const [selectedCampus, setSelectedCampus] = useState<Campus>();
    const [selectedBuilding, setSelectedBuilding] = useState<Building>();

    const [isMondaySelected, setIsMondaySelected] = useState<boolean>(false);
    const [isTuesdaySelected, setIsTuesdaySelected] = useState<boolean>(false);
    const [isWednesdaySelected, setIsWednesdaySelected] = useState<boolean>(false);
    const [isThursdaySelected, setIsThursdaySelected] = useState<boolean>(false);
    const [isFridaySelected, setIsFridaySelected] = useState<boolean>(false);

    const [startDate, setStartDate] = useState<Date>(new Date());
    const [endDate, setEndDate] = useState<Date>(new Date());
    const [startTime, setStartTime] = useState<string>('10:00');
    const [endTime, setEndTime] = useState<string>('11:00');
    
    const [room, setRoom] = useState<string>();

    useEffect(() => {
        if (campuses) {
            setSelectedCampus(campuses[0]);
            setSelectedBuilding(campuses[0].buildings[0])
        }
    }, [tutoring, campuses]);

    useImperativeHandle(ref, () => ({
        handleSubmit
    }));

    const onCampusChange = (event: ChangeEvent<HTMLSelectElement>) => {
        const campus = campuses.find(campus => campus.id === event.target.value);
        setSelectedCampus(campus);
        setSelectedBuilding(campus.buildings[0]);
    }

    const onBuildingChange = (event : ChangeEvent<HTMLSelectElement>) => {
        const building = selectedCampus.buildings.find(building => building.id === event.target.value);
        setSelectedBuilding(building);
    }

    const handleSubmit = () => {
        const params = new FormData();

        params.append('batch_session_creation_form[tutoring]', tutoring.id);

        params.append('batch_session_creation_form[mondaySelected]', isMondaySelected.toString());
        params.append('batch_session_creation_form[tuesdaySelected]', isTuesdaySelected.toString());
        params.append('batch_session_creation_form[wednesdaySelected]', isWednesdaySelected.toString());
        params.append('batch_session_creation_form[thursdaySelected]', isThursdaySelected.toString());
        params.append('batch_session_creation_form[fridaySelected]', isFridaySelected.toString());

        params.append('batch_session_creation_form[startDate][year]', startDate.getFullYear().toString());
        params.append('batch_session_creation_form[startDate][month]', (startDate.getMonth() + 1).toString());
        params.append('batch_session_creation_form[startDate][day]', startDate.getDate().toString());

        params.append('batch_session_creation_form[endDate][year]', endDate.getFullYear().toString());
        params.append('batch_session_creation_form[endDate][month]', (endDate.getMonth() + 1).toString());
        params.append('batch_session_creation_form[endDate][day]', endDate.getDate().toString());

        params.append('batch_session_creation_form[startTime][hour]', parseInt(startTime.split(':')[0]).toString());
        params.append('batch_session_creation_form[startTime][minute]', parseInt(startTime.split(':')[1]).toString());

        params.append('batch_session_creation_form[endTime][hour]', parseInt(endTime.split(':')[0]).toString());
        params.append('batch_session_creation_form[endTime][minute]', parseInt(endTime.split(':')[1]).toString());

        params.append('batch_session_creation_form[building]', selectedBuilding.id);
        params.append('batch_session_creation_form[room]', room);
        console.log(params)

        fetch(Routing.generate("batch_create_sessions"), {
                method: 'POST',
                body: params
            })
            .then(() => toggleModal());
    }

    return (
        <div className='d-flex flex-column flex-lg-row'>
                    <div className='multiple-session-creation-info flex-1'>
                        <div className='days line'>
                            <div className='label'>
                                Jours par défaut :
                            </div>
                            <div className='d-flex flex-column flex-lg-row'>
                                <Form.Check 
                                        label='monday'
                                        type='checkbox'
                                        checked={isMondaySelected}
                                        onChange={(event) => setIsMondaySelected(event.target.checked)}
                                />
                                <Form.Check 
                                        className='ms-lg-2'
                                        label='tuesday'
                                        type='checkbox'
                                        checked={isTuesdaySelected}
                                        onChange={(event) => setIsTuesdaySelected(event.target.checked)}
                                />
                                <Form.Check 
                                        className='ms-lg-2'
                                        label='wednesday'
                                        type='checkbox'
                                        checked={isWednesdaySelected}
                                        onChange={(event) => setIsWednesdaySelected(event.target.checked)}
                                />
                                <Form.Check 
                                        className='ms-lg-2'
                                        label='thursday'
                                        type='checkbox'
                                        checked={isThursdaySelected}
                                        onChange={(event) => setIsThursdaySelected(event.target.checked)}
                                />
                                <Form.Check 
                                        className='ms-lg-2'
                                        label='friday'
                                        type='checkbox'
                                        checked={isFridaySelected}
                                        onChange={(event) => setIsFridaySelected(event.target.checked)}
                                />
                            </div>
                        </div>
                        <div className='hours line'>
                            <div className='label'>
                                Horaires par défaut :
                            </div>
                            <div className='d-flex align-items-center'>
                                <span className='pe-2'>De : </span>
                                <div className='start-time-value pe-2'>
                                    <TimePicker 
                                        value={startTime}
                                        onChange={(time: string) => setStartTime(time)}
                                        
                                    />
                                </div>
                                <span className='pe-2'>A : </span>
                                <div className='end-time-value'>
                                    <TimePicker 
                                        value={endTime} 
                                        onChange={(time: string) => setEndTime(time)}
                                    />
                                </div>
                            </div>
                        </div>
                        <hr className='hr'></hr>
                        <div className='date-range d-flex line'>
                            <div className='start-date flex-grow-1'>
                                <div className='start-date-label label'>
                                    Première date :
                                </div>
                                <div className='start-date-value'>
                                    <DatePicker
                                        value={startDate} 
                                        onChange={(date: Date) => setStartDate(date)}
                                    />
                                </div>
                            </div>
                            <div className='end-date flex-grow-1'>
                                <div className='end-date-label label'>
                                    Dernière date :
                                </div>
                                <div className='end-date-value'>
                                    <DatePicker 
                                        value={endDate} 
                                        onChange={(date: Date) => setEndDate(date)}
                                    />                               
                                </div>
                            </div>
                        </div>
                        <div className='place line'>
                            <div className='place-label label'>
                                Campus, bâtiment, salle :
                            </div>
                            <div className='place-value d-flex'>
                                <Form.Select className='campus flex-grow-1 me-2' onChange={onCampusChange}>
                                    {campuses? campuses.map((campus, index) =>
                                        <option key={`campus-${index}`} value={campus.id}>
                                            {campus.name}
                                        </option>
                                    ) : null}
                                </Form.Select>
                                
                                <Form.Select className='building flex-grow-1 me-2' onChange={onBuildingChange}>
                                    {selectedCampus? selectedCampus.buildings.map((building, index) =>
                                        <option key={`building-${index}`} value={building.id}>
                                            {building.name}
                                        </option>
                                    ): null}
                                </Form.Select>
                                <Form.Control value={room} className='room flex-grow-1'></Form.Control>
                            </div>
                        </div>
                    </div>
                    <div className='multiple-session-static-info flex-grow-1'>
                        <div className='tutoring line'>
                            <div className='tutoring-label label'>
                                Tutorat
                            </div>
                            <div>
                                <Form.Control disabled value={tutoring.name}></Form.Control>
                            </div>
                        </div>
                        <div className='tutors line'>
                            <div className='tutors-label label'>
                                Tuteurs
                            </div>
                            <div className='tutors-list d-flex'>
                                {tutoring.tutors.map((tutor, index) => 
                                    <Badge
                                        key={`tutor-${index}`}
                                        className={index !== 0 ? 'ms-2' : ''}
                                        bg="secondary"
                                    >
                                        {tutor.lastName} {tutor.firstName}
                                    </Badge>
                                )}
                            </div>
                        </div>
                    </div>
        </div>
    );
});

export default BatchTutoringSessionCreationModal;