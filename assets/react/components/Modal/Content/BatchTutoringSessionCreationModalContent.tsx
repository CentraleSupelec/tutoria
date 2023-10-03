import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import React, { ChangeEvent, useCallback, useEffect, useState } from 'react';
import { Badge, Button, Form, Spinner } from 'react-bootstrap';
import DatePicker from 'react-date-picker';
import { useTranslation } from "react-i18next";
import TimePicker from 'react-time-picker';
import Routing from "../../../../Routing";
import Building from '../../../interfaces/Building';
import Campus from '../../../interfaces/Campus';
import ModalErrorsInterface from '../../../interfaces/ModalErrorsInterface';
import Tutoring from '../../../interfaces/Tutoring';
import { daysArrayToDaysSelection, parseErrors } from '../../../utils';
import GeneralErrorsRenderer from '../../GeneralErrorsRenderer';

interface BatchTutoringSessionCreationModalContentProps {
    tutoring: Tutoring,
    campuses: Campus[],
    toggleModal: Function,
    saveTutoring: boolean,
    onUpdate: Function
}

interface BatchTutoringSessionCreationModalContentErrors extends ModalErrorsInterface {
    weekDays?: string,
    room?: string,
    startDate?: string,
    endDate?: string,
    endTime?: string,
}

type ValuePiece = Date | string | null;

type Value = ValuePiece | [ValuePiece, ValuePiece];

const defaultStartTime = new Date();
defaultStartTime.setHours(16);
defaultStartTime.setMinutes(0);

const defaultEndTime = new Date();
defaultEndTime.setHours(18);
defaultEndTime.setMinutes(0);

const defaultDaySelection: DaysSelection = {
    monday: false,
    tuesday: false,
    wednesday: false,
    thursday: false,
    friday: false
}

export default function ({ tutoring, campuses, toggleModal, saveTutoring, onUpdate }: BatchTutoringSessionCreationModalContentProps) {
    const { t } = useTranslation();

    const [ready, setReady] = useState(false);
    const [disableSaveButton, setDisableSaveButton] = useState(false);
    const [selectedCampus, setSelectedCampus] = useState<Campus>();
    const [selectedBuilding, setSelectedBuilding] = useState<Building>();
    const [defaultWeekDays, setDefaultWeekDays] = useState<DaysSelection>(defaultDaySelection);
    const [startDate, setStartDate] = useState<Value>(new Date());
    const [endDate, setEndDate] = useState<Value>(new Date());
    const [startTime, setStartTime] = useState<Value>(defaultStartTime);
    const [endTime, setEndTime] = useState<Value>(defaultEndTime);
    const [room, setRoom] = useState<string>('');
    const [errors, setErrors] = useState<BatchTutoringSessionCreationModalContentErrors>({generalErrors: []});

    const removeError = (errorName: string) => {
        if (errors[errorName]) {
            let newErrors = {...errors};
            delete newErrors[errorName];
            setErrors(newErrors);
        }
    }

    const onWeekDaysChange = (event: ChangeEvent<HTMLInputElement>, day: string) => {
        setDefaultWeekDays({...defaultWeekDays, [day]: event.target.checked})
        removeError('weekDays');
    }

    const onCampusChange = useCallback((event: ChangeEvent<HTMLSelectElement>) => {
        const campus = campuses.find(campus => campus.id === event.target.value);
        setSelectedCampus(campus);
        setSelectedBuilding(campus?.buildings[0]);
    }, [campuses])

    const onBuildingChange = (event: ChangeEvent<HTMLSelectElement>) => {
        const building = selectedCampus?.buildings.find(building => building.id === event.target.value);
        setSelectedBuilding(building);
    }

    const onTimeChange = (time: Value, start: boolean) => {
        if (!(typeof time === 'string')) return;
        let setter = setEndTime;

        if (start) {
            setter = setStartTime
        }

        const newTime = new Date();
        newTime.setHours(parseInt(time.split(':')[0]))
        newTime.setMinutes(parseInt(time.split(':')[1]))
        setter(newTime);
        removeError('endTime');
    }

    const onDateChange = (date: Value, start: boolean) => {
        let setter = setEndDate;
        if (start) {
            setter = setStartDate
        }
        setter(date);
        removeError('endDate');
        removeError('startDate');
    }

    const handleSubmit = () => {
        setDisableSaveButton(true);

        const params = new FormData();

        params.append('batch_tutoring_session_creation[tutoring]', tutoring.id);

        Object.keys(defaultWeekDays).forEach((day) => {
            if (defaultWeekDays[day]) {
                params.append(`batch_tutoring_session_creation[weekDays][]`, day);
            }
        });

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

        if (startTime && startTime instanceof Date) {
            params.append('batch_tutoring_session_creation[startTime][hour]', startTime.getHours().toString());
            params.append('batch_tutoring_session_creation[startTime][minute]', startTime.getMinutes().toString());
        }

        if (endTime && endTime instanceof Date) {
            params.append('batch_tutoring_session_creation[endTime][hour]', endTime.getHours().toString());
            params.append('batch_tutoring_session_creation[endTime][minute]', endTime.getMinutes().toString());
        }

        params.append('batch_tutoring_session_creation[building]', selectedBuilding ? selectedBuilding.id : '');
        params.append('batch_tutoring_session_creation[room]', room ?? '');
        params.append('batch_tutoring_session_creation[saveDefaultValues]', saveTutoring.toString());

        fetch(Routing.generate("batch_create_sessions"), {
                method: 'POST',
                body: params
            })
            .then(response => {
                setDisableSaveButton(false);
                if (response.status === 200) {
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.errors) {
                    setErrors(parseErrors(data.errors));
                } else {
                    onUpdate();
                    toggleModal();
                }
            });
    }

    useEffect(() => {
        if (campuses) {
            if (tutoring.defaultBuilding && tutoring.defaultBuilding.campus) {
                const tutoringDefaultCampus = campuses.find(campus => campus.id === tutoring.defaultBuilding.campus.id);
                setSelectedCampus(tutoringDefaultCampus);
                setSelectedBuilding(tutoring.defaultBuilding);
            } else {
                setSelectedCampus(campuses[0]);
                setSelectedBuilding(campuses[0].buildings[0])
            }
            setReady(true);
        }
    }, [tutoring, campuses]);

    useEffect(() => {
        setRoom(tutoring.defaultRoom?? '');
        setDefaultWeekDays(daysArrayToDaysSelection(tutoring.defaultWeekDays));

        if (tutoring.defaultStartTime) {
            setStartTime(new Date(tutoring.defaultStartTime));
        }

        if (tutoring.defaultEndTime) {
            setEndTime(new Date(tutoring.defaultEndTime));
        }
    }, [tutoring])

    return <>
        {ready ?
            <>
                <GeneralErrorsRenderer errors={errors.generalErrors} />
                <div className='d-flex flex-column flex-lg-row'>
                    <div className='multiple-session-creation-info'>
                        <div className='days line'>
                            <div className='label'>
                                {t('form.default_days')}
                            </div>
                            <div className='d-flex flex-column flex-lg-row'>
                                {Object.keys(defaultWeekDays).map(day =>
                                    <Form.Check
                                        key={`day-selection-${day}`}
                                        className={day !== 'monday' ? 'ms-lg-2' : ''}
                                        label={t(`utils.days.${day}`)}
                                        type='checkbox'
                                        checked={defaultWeekDays[day]}
                                        onChange={(event) => onWeekDaysChange(event, day)}
                                        isInvalid={!!errors.weekDays}
                                    />
                                )}
                            </div>
                            <Form.Control
                                hidden={true}
                                isInvalid={!!errors.weekDays}
                            />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.weekDays}
                            </Form.Control.Feedback>
                        </div>
                        <div className='hours line'>
                            <div className='label'>
                                {t('form.default_hours')}
                            </div>
                            <div className={'d-flex justify-content-between' + (errors.endTime? ' invalid': '')}>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.from')} </span>
                                    <div className='start-time-value pe-2'>
                                        <TimePicker
                                            value={startTime}
                                            onChange={(time: Value) => onTimeChange(time, true)}
                                            clockIcon={null}
                                        />
                                    </div>
                                </div>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.to')}</span>
                                    <div className='end-time-value'>
                                        <TimePicker
                                            value={endTime}
                                            onChange={(time: Value) => onTimeChange(time, false)}
                                            clockIcon={null}
                                        />
                                    </div>
                                </div>
                            </div>
                            <Form.Control
                                hidden={true}
                                isInvalid={!!errors.endTime}
                            />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.endTime}
                            </Form.Control.Feedback>
                        </div>
                        <hr className='hr'></hr>
                        <div className='date-range line'>
                            <div className={'d-flex' + (errors.endDate || errors.startDate? ' invalid': '')}>
                                <div className='start-date flex-grow-1'>
                                    <div className='start-date-label label'>
                                        {t('form.start_date')}
                                    </div>
                                    <div className='start-date-value'>
                                        <DatePicker
                                            value={startDate}
                                            onChange={(date: Value) => onDateChange(date, true)}
                                            calendarIcon={<FontAwesomeIcon className='text-secondary' icon="calendar-days" />}
                                            clearIcon={null}
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
                                            onChange={(date: Value) => onDateChange(date, false)}
                                            calendarIcon={<FontAwesomeIcon className='text-secondary' icon="calendar-days" />}
                                            clearIcon={null}
                                        />
                                    </div>
                                </div>
                            </div>
                            <Form.Control hidden={true} isInvalid={!!errors.endDate} />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.endDate}
                            </Form.Control.Feedback>
                            <Form.Control hidden={true} isInvalid={!!errors.startDate} />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.startDate}
                            </Form.Control.Feedback>
                        </div>

                        <div className='place line d-flex flex-column flex-lg-row'>
                            <div className='campus d-flex flex-column flex-grow-1 me-2 pb-2'>
                                <div className='campus-label label'>
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
                                <div className='building-label label'>
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
                                <div className='room-label label'>
                                    {t('form.room')}
                                </div>
                                <Form.Control
                                    value={room}
                                    onChange={e => {
                                            setRoom(e.target.value);
                                            removeError('room')
                                        }
                                    }
                                    isInvalid={!!errors.room}
                                />
                                <Form.Control.Feedback className='mt-2' type='invalid'>
                                    {errors.room}
                                </Form.Control.Feedback>
                            </div>
                        </div>
                    </div>
                    <div className='multiple-session-static-info'>
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
                                        bg="tertiary"
                                    >
                                        {tutor.lastName} {tutor.firstName}
                                    </Badge>
                                )}
                            </div>
                        </div>
                        <div className="card border-info bg-info bg-opacity-25">
                            <div className="card-body">
                                <small>{t('form.note_edit_tutors')}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div className='d-flex justify-content-end'>
                    <Button type='submit' variant='secondary' onClick={handleSubmit} disabled={disableSaveButton}>
                        {saveTutoring ? t('form.save_and_batch_create_button') : t('form.create_button')}
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
