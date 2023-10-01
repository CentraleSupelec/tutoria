import React, { ChangeEvent, useCallback, useEffect, useState } from 'react';
import { Badge, Button, Form, Spinner } from 'react-bootstrap';
import { useTranslation } from "react-i18next";
import TimePicker from 'react-time-picker';
import Routing from "../../../../Routing";
import Building from '../../../interfaces/Building';
import Campus from '../../../interfaces/Campus';
import ModalErrorsInterface from '../../../interfaces/ModalErrorsInterface';
import Tutoring from '../../../interfaces/Tutoring';
import { daysArrayToDaysSelection, parseErrors } from '../../../utils';
import GeneralErrorsRenderer from '../../GeneralErrorsRenderer';

interface TutoringModalContentProps {
    tutoring: Tutoring,
    campuses: Campus[],
    toggleModal: Function,
    onUpdate: Function
}

interface TutoringModalContentErrors extends ModalErrorsInterface {
    defaultWeekDays?: string,
    defaultRoom?: string,
    defaultEndTime?: string,
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

export default function ({ tutoring, campuses, toggleModal, onUpdate }: TutoringModalContentProps) {
    const { t } = useTranslation();

    const [ready, setReady] = useState(false);
    const [disableSaveButton, setDisableSaveButton] = useState(false);
    const [selectedCampus, setSelectedCampus] = useState<Campus>();
    const [selectedBuilding, setSelectedBuilding] = useState<Building>();
    const [defaultWeekDays, setDefaultWeekDays] = useState<DaysSelection>(defaultDaySelection);
    const [startTime, setStartTime] = useState<Value>(defaultStartTime);
    const [endTime, setEndTime] = useState<Value>(defaultEndTime);
    const [room, setRoom] = useState<string>('');
    const [errors, setErrors] = useState<TutoringModalContentErrors>({generalErrors: []});

    const removeError = (errorName: string) => {
        if (errors[errorName]) {
            let newErrors = {...errors};
            delete newErrors[errorName];
            setErrors(newErrors);
        }
    }

    const onWeekDaysChange = (event: ChangeEvent<HTMLInputElement>, day: string) => {
        setDefaultWeekDays({...defaultWeekDays, [day]: event.target.checked})
        removeError('defaultWeekDays');
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
        removeError('defaultEndTime');
    }

    const handleSubmit = () => {
        setDisableSaveButton(true);

        const params = new FormData();

        Object.keys(defaultWeekDays).forEach(day => {
            if (defaultWeekDays[day]) {
                params.append(`tutoring[defaultWeekDays][]`, day);
            }
        });

        if (startTime && startTime instanceof Date) {
            params.append('tutoring[defaultStartTime][hour]', startTime.getHours().toString());
            params.append('tutoring[defaultStartTime][minute]', startTime.getMinutes().toString());
        }

        if (endTime && endTime instanceof Date) {
            params.append('tutoring[defaultEndTime][hour]', endTime.getHours().toString());
            params.append('tutoring[defaultEndTime][minute]', endTime.getMinutes().toString());
        }

        params.append('tutoring[defaultBuilding]', selectedBuilding? selectedBuilding.id: '');
        params.append('tutoring[defaultRoom]', room?? '');

        fetch(Routing.generate("update_tutoring", {id: tutoring.id}), {
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
                setRoom(tutoring.defaultRoom?? '');
            } else {
                setSelectedCampus(campuses[0]);
                setSelectedBuilding(campuses[0].buildings[0]);
            }
            setReady(true);
        }
    }, [tutoring, campuses]);

    useEffect(() => {
        setDefaultWeekDays(daysArrayToDaysSelection(tutoring.defaultWeekDays));
        setStartTime(new Date(tutoring.defaultStartTime));
        setEndTime(new Date(tutoring.defaultEndTime));
    }, [tutoring])

    return <>
        {ready ?
            <>
                <GeneralErrorsRenderer errors={errors.generalErrors} />
                <div className="card border-info bg-info bg-opacity-25 mb-2">
                    <div className="card-body">
                        <small>{t('form.note_edit_tutoring')}</small>
                    </div>
                </div>
                <div className='d-flex flex-column flex-lg-row'>
                    <div className='multiple-session-creation-info flex-1'>
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
                                        isInvalid={!!errors.defaultWeekDays}
                                    />
                                )}
                            </div>
                            <Form.Control
                                hidden={true}
                                isInvalid={!!errors.defaultWeekDays}
                            />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.defaultWeekDays}
                            </Form.Control.Feedback>
                        </div>
                        <div className='hours line'>
                            <div className='label'>
                                {t('form.default_hours')}
                            </div>
                            <div className={'d-flex justify-content-between' + (errors.defaultEndTime? ' invalid': '')}>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.from')} </span>
                                    <div className='start-time-value pe-2'>
                                        <TimePicker
                                            value={startTime}
                                            onChange={(time: Value) => onTimeChange(time, true)}
                                            clockIcon={null}
                                            clearIcon={null}
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
                                            clearIcon={null}
                                        />
                                    </div>
                                </div>
                            </div>
                            <Form.Control
                                hidden={true}
                                isInvalid={!!errors.defaultEndTime}
                            />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.defaultEndTime}
                            </Form.Control.Feedback>
                        </div>
                        <hr className='hr'></hr>

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
                                            removeError('defaultRoom')
                                        }
                                    }
                                    isInvalid={!!errors.defaultRoom}
                                />
                                <Form.Control.Feedback className='mt-2' type='invalid'>
                                    {errors.defaultRoom}
                                </Form.Control.Feedback>
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
                                        bg="tertiary"
                                    >
                                        {tutor.lastName} {tutor.firstName}
                                    </Badge>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
                <div className='d-flex justify-content-end'>
                    <Button type='submit' variant='secondary' onClick={handleSubmit} disabled={disableSaveButton}>
                        {t('form.save_button')}
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
