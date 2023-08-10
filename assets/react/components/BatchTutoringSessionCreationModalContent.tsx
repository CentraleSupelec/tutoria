import React, { ChangeEvent, useCallback, useEffect, useState } from 'react';
import { Badge, Button, Form, Spinner } from 'react-bootstrap';
import DatePicker from 'react-date-picker';
import { useTranslation } from "react-i18next";
import TimePicker from 'react-time-picker';
import Routing from "../../Routing";
import Building from '../interfaces/Building';
import Campus from '../interfaces/Campus';
import Tutoring from '../interfaces/Tutoring';

interface BatchTutoringSessionCreationModalContentProps {
    tutoring: Tutoring,
    campuses: Campus[],
    toggleModal: Function,
}

type ValuePiece = Date | string | null;

type Value = ValuePiece | [ValuePiece, ValuePiece];

const BatchTutoringSessionCreationModalContent = ({ tutoring, campuses, toggleModal }: BatchTutoringSessionCreationModalContentProps) => {
    const { t } = useTranslation();
    const [ready, setReady] = useState(false);
    const [selectedCampus, setSelectedCampus] = useState<Campus>();
    const [selectedBuilding, setSelectedBuilding] = useState<Building>();

    const [isMondaySelected, setIsMondaySelected] = useState<boolean>(false);
    const [isTuesdaySelected, setIsTuesdaySelected] = useState<boolean>(false);
    const [isWednesdaySelected, setIsWednesdaySelected] = useState<boolean>(false);
    const [isThursdaySelected, setIsThursdaySelected] = useState<boolean>(false);
    const [isFridaySelected, setIsFridaySelected] = useState<boolean>(false);

    const [startDate, setStartDate] = useState<Value>(new Date());
    const [endDate, setEndDate] = useState<Value>(new Date());
    const [startTime, setStartTime] = useState<Value>('10:00');
    const [endTime, setEndTime] = useState<Value>('11:00');

    const [room, setRoom] = useState<string>('');

    const onCampusChange = useCallback((event: ChangeEvent<HTMLSelectElement>) => {
        const campus = campuses.find(campus => campus.id === event.target.value);
        setSelectedCampus(campus);
        setSelectedBuilding(campus?.buildings[0]);
    }, [campuses])

    const onBuildingChange = (event: ChangeEvent<HTMLSelectElement>) => {
        const building = selectedCampus?.buildings.find(building => building.id === event.target.value);
        setSelectedBuilding(building);
    }

    const handleSubmit = () => {
        const params = new FormData();

        params.append('batch_tutoring_session_creation[tutoring]', tutoring.id);

        params.append('batch_tutoring_session_creation[mondaySelected]', isMondaySelected.toString());
        params.append('batch_tutoring_session_creation[tuesdaySelected]', isTuesdaySelected.toString());
        params.append('batch_tutoring_session_creation[wednesdaySelected]', isWednesdaySelected.toString());
        params.append('batch_tutoring_session_creation[thursdaySelected]', isThursdaySelected.toString());
        params.append('batch_tutoring_session_creation[fridaySelected]', isFridaySelected.toString());

        if (startDate && startDate instanceof Date) {
            params.append('batch_tutoring_session_creation[startDate][year]', startDate.getFullYear().toString());
            params.append('batch_tutoring_session_creation[startDate][month]', (startDate.getMonth() + 1).toString());
            params.append('batch_tutoring_session_creation[startDate][day]', startDate.getDate().toString());
        }

        if (endDate && endDate instanceof Date) {
            params.append('batch_tutoring_session_creation[endDate][year]', endDate.getFullYear().toString());
            params.append('batch_tutoring_session_creation[endDate][month]', (endDate.getMonth() + 1).toString());
            params.append('batch_tutoring_session_creation[endDate][day]', endDate.getDate().toString());
        }

        if (startTime && typeof startTime === 'string') {
            params.append('batch_tutoring_session_creation[startTime][hour]', parseInt(startTime.split(':')[0]).toString());
            params.append('batch_tutoring_session_creation[startTime][minute]', parseInt(startTime.split(':')[1]).toString());
        }

        if (endTime && typeof endTime === 'string') {
            params.append('batch_tutoring_session_creation[endTime][hour]', parseInt(endTime.split(':')[0]).toString());
            params.append('batch_tutoring_session_creation[endTime][minute]', parseInt(endTime.split(':')[1]).toString());
        }

        params.append('batch_tutoring_session_creation[building]', selectedBuilding ? selectedBuilding.id : '');
        params.append('batch_tutoring_session_creation[room]', room ?? '');

        fetch(Routing.generate("batch_create_sessions"), {
            method: 'POST',
            body: params
        })
            .then(() => toggleModal());
    }

    useEffect(() => {
        if (campuses) {
            if (tutoring.building && tutoring.building.campus) {
                const tutoringDefaultCampus = campuses.find(campus => campus.id === tutoring.building.campus.id);
                setSelectedCampus(tutoringDefaultCampus);
                setSelectedBuilding(tutoring.building);
            } else {
                setSelectedCampus(campuses[0]);
                setSelectedBuilding(campuses[0].buildings[0])
            }
            setReady(true);
        }

        if (tutoring.room) {
            setRoom(tutoring.room);
        }
    }, [tutoring, campuses]);

    return <>
        {ready ?
            <>
                <div className='d-flex flex-column flex-lg-row'>
                    <div className='multiple-session-creation-info flex-1'>
                        <div className='days line'>
                            <div className='label'>
                                {t('form.default_days')}
                            </div>
                            <div className='d-flex flex-column flex-lg-row'>
                                <Form.Check
                                    label={t('utils.days.monday')}
                                    type='checkbox'
                                    checked={isMondaySelected}
                                    onChange={(event) => setIsMondaySelected(event.target.checked)}
                                />
                                <Form.Check
                                    className='ms-lg-2'
                                    label={t('utils.days.tuesday')}
                                    type='checkbox'
                                    checked={isTuesdaySelected}
                                    onChange={(event) => setIsTuesdaySelected(event.target.checked)}
                                />
                                <Form.Check
                                    className='ms-lg-2'
                                    label={t('utils.days.wednesday')}
                                    type='checkbox'
                                    checked={isWednesdaySelected}
                                    onChange={(event) => setIsWednesdaySelected(event.target.checked)}
                                />
                                <Form.Check
                                    className='ms-lg-2'
                                    label={t('utils.days.thursday')}
                                    type='checkbox'
                                    checked={isThursdaySelected}
                                    onChange={(event) => setIsThursdaySelected(event.target.checked)}
                                />
                                <Form.Check
                                    className='ms-lg-2'
                                    label={t('utils.days.friday')}
                                    type='checkbox'
                                    checked={isFridaySelected}
                                    onChange={(event) => setIsFridaySelected(event.target.checked)}
                                />
                            </div>
                        </div>
                        <div className='hours line'>
                            <div className='label'>
                                {t('form.default_hours')}
                            </div>
                            <div className='d-flex justify-content-between'>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.from')} </span>
                                    <div className='start-time-value pe-2'>
                                        <TimePicker
                                            value={startTime}
                                            onChange={(time: Value) => setStartTime(time)}
                                            clockIcon={null}
                                        />
                                    </div>
                                </div>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.to')}</span>
                                    <div className='end-time-value'>
                                        <TimePicker
                                            value={endTime}
                                            onChange={(time: Value) => setEndTime(time)}
                                            clockIcon={null}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr className='hr'></hr>
                        <div className='date-range d-flex line'>
                            <div className='start-date flex-grow-1'>
                                <div className='start-date-label label'>
                                    {t('form.start_date')}
                                </div>
                                <div className='start-date-value'>
                                    <DatePicker
                                        value={startDate}
                                        onChange={(date: Value) => setStartDate(date)}
                                    />
                                </div>
                            </div>
                            <div className='end-date flex-grow-1'>
                                <div className='end-date-label label'>
                                    {t('form.end_date')}
                                </div>
                                <div className='end-date-value'>
                                    <DatePicker
                                        value={endDate}
                                        onChange={(date: Value) => setEndDate(date)}
                                    />
                                </div>
                            </div>
                        </div>
                        <div className='place line d-flex flex-column flex-lg-row'>
                            <div className='campus d-flex flex-column flex-grow-1 me-2 pb-2'>
                                <div className='campus-label label ps-2'>
                                    {t('form.campus')}
                                </div>
                                <Form.Select value={selectedCampus?.id} onChange={onCampusChange}>
                                    {campuses ? campuses.map((campus, index) =>
                                        <option key={`campus-${index}`} value={campus.id}>
                                            {campus.name}
                                        </option>
                                    ) : null}
                                </Form.Select>
                            </div>
                            <div className='building d-flex flex-column flex-grow-1 me-2 pb-2'>
                                <div className='building-label label ps-2'>
                                    {t('form.building')}
                                </div>
                                <Form.Select value={selectedBuilding?.id} onChange={onBuildingChange}>
                                    {selectedCampus ? selectedCampus.buildings.map((building, index) =>
                                        <option key={`building-${index}`} value={building.id}>
                                            {building.name}
                                        </option>
                                    ) : null}
                                </Form.Select>
                            </div>

                            <div className='room d-flex flex-column flex-grow-1 pb-2'>
                                <div className='room-label label ps-2'>
                                    {t('form.room')}
                                </div>
                                <Form.Control value={room} onChange={e => setRoom(e.target.value)}></Form.Control>
                            </div>
                        </div>
                    </div>
                    <div className='multiple-session-static-info flex-grow-1'>
                        <div className='tutoring line'>
                            <div className='tutoring-label label'>
                                {t('form.tutoring')}
                            </div>
                            <div>
                                <Form.Control disabled value={tutoring.name}></Form.Control>
                            </div>
                        </div>
                        <div className='tutors line'>
                            <div className='tutors-label label'>
                                {t('form.tutors')}
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
                <div className='d-flex justify-content-end'>
                    <Button type='submit' variant="success" onClick={handleSubmit}>
                        {t('form.batch_create_button')}
                    </Button>
                </div>
            </>
            :
            <div className='d-flex justify-content-center'>
                <Spinner animation='border' />
            </div>
        }
    </>;
};

export default BatchTutoringSessionCreationModalContent;
