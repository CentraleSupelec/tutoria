import React, { ChangeEvent, useEffect, useState } from 'react';
import { Badge, Button, Form } from 'react-bootstrap';
import { useTranslation } from "react-i18next";
import Routing from "../../../../Routing";
import Building from '../../../interfaces/Building';
import Campus from '../../../interfaces/Campus';
import Tutoring from '../../../interfaces/Tutoring';
import TutoringSession from '../../../interfaces/TutoringSession';
import DateTimePicker from 'react-datetime-picker';
import { Value } from 'react-datetime-picker/dist/cjs/shared/types';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import ModalErrorsInterface from '../../../interfaces/ModalErrorsInterface';
import { parseErrors } from '../../../utils';
import GeneralErrorsRenderer from '../../GeneralErrorsRenderer';

interface TutoringSessionModalContentProps {
    tutoring: Tutoring,
    tutoringSession?: TutoringSession
    campuses: Campus[],
    toggleModal: Function,
    updateTutoring?: Function,
    updateTutoringSession?: Function,
}

interface TutoringSessionModalContentErrors extends ModalErrorsInterface {
    endDateTime?: string,
    room?: string,
    onlineMeetingUri?: string
}

interface RouteParams {
    id?: string
}

export default function ({ tutoring, tutoringSession, campuses, toggleModal, updateTutoring, updateTutoringSession }: TutoringSessionModalContentProps) {
    const { t } = useTranslation();
    const [ready, setReady] = useState(false);
    const [disableSaveButton, setDisableSaveButton] = useState(false);
    const [selectedCampus, setSelectedCampus] = useState<Campus>();
    const [selectedBuilding, setSelectedBuilding] = useState<Building>();
    const [isRemote, setIsRemote] = useState<boolean>(false);
    const [onlineMeetingUri, setOnlineMeetingUri] = useState<string>('');
    const [startDateTime, setStartDateTime] = useState<Date|null>(new Date());
    const [endDateTime, setEndDateTime] = useState<Date|null>(new Date());
    const [room, setRoom] = useState<string>('');
    const [errors, setErrors] = useState<TutoringSessionModalContentErrors>({generalErrors: []});

    useEffect(() => {
        if (campuses) {
            if (tutoringSession?.building && tutoringSession.building.campus) {
                const tutoringSessionCampus = campuses.find(campus => campus.id === tutoringSession.building.campus.id);
                setSelectedCampus(tutoringSessionCampus);
                setSelectedBuilding(tutoringSession.building);
                setRoom(tutoringSession.room?? '');
            } else if (tutoring?.defaultBuilding && tutoring.defaultBuilding.campus) {
                const tutoringDefaultCampus = campuses.find(campus => campus.id === tutoring.defaultBuilding.campus.id);
                setSelectedCampus(tutoringDefaultCampus);
                setSelectedBuilding(tutoring.defaultBuilding);
                setRoom(tutoring.defaultRoom?? '');
            } else {
                setSelectedCampus(campuses[0]);
                setSelectedBuilding(campuses[0].buildings[0])
            }
            setReady(true);
        }
    }, [tutoring, tutoringSession, campuses]);

    useEffect(() => {
        if (tutoringSession) {
            setStartDateTime(new Date(tutoringSession.startDateTime));
            setEndDateTime(new Date(tutoringSession.endDateTime));
            setIsRemote(tutoringSession.isRemote);
            setOnlineMeetingUri(tutoringSession.onlineMeetingUri?? '');
        }
    }, [tutoringSession])

    const removeError = (errorName: string) => {
        if (errors[errorName]) {
            let newErrors = {...errors};
            delete newErrors[errorName];
            setErrors(newErrors);
        }
    }

    const onDateTimeChange = (date: Value, start: boolean) => {
        let setter = setEndDateTime;
        if (start) {
            setter = setStartDateTime
        }
        setter(date);
        removeError('endDateTime');
    }

    const onCampusChange = (event: ChangeEvent<HTMLSelectElement>) => {
        const campus = campuses.find(campus => campus.id === event.target.value);
        if (campus) {
            setSelectedCampus(campus);
            setSelectedBuilding(campus.buildings[0]);
        }
    }

    const onBuildingChange = (event: ChangeEvent<HTMLSelectElement>) => {
        if (selectedCampus) {
            const building = selectedCampus.buildings.find(building => building.id === event.target.value);
            setSelectedBuilding(building);
        }
    }

    const handleSubmit = () => {
        setDisableSaveButton(true);

        const params = new FormData();
        let routeName = 'create_tutoring_session';
        let routeParams : RouteParams = {}

        if (tutoringSession) {
            routeName = 'update_tutoring_session'
            routeParams.id = tutoringSession.id;
        }

        params.append('tutoring_session[tutoring]', tutoring.id);
        params.append('tutoring_session[isRemote]', isRemote.toString());

        if (startDateTime) {
            params.append('tutoring_session[startDateTime][date][year]', startDateTime.getFullYear().toString());
            params.append('tutoring_session[startDateTime][date][month]', (startDateTime.getMonth() + 1).toString());
            params.append('tutoring_session[startDateTime][date][day]', startDateTime.getDate().toString());
            params.append('tutoring_session[startDateTime][time][hour]', startDateTime.getHours().toString());
            params.append('tutoring_session[startDateTime][time][minute]', startDateTime.getMinutes().toString());
        }

        if (endDateTime) {
            params.append('tutoring_session[endDateTime][date][year]', endDateTime.getFullYear().toString());
            params.append('tutoring_session[endDateTime][date][month]', (endDateTime.getMonth() + 1).toString());
            params.append('tutoring_session[endDateTime][date][day]', endDateTime.getDate().toString());
            params.append('tutoring_session[endDateTime][time][hour]', endDateTime.getHours().toString());
            params.append('tutoring_session[endDateTime][time][minute]', endDateTime.getMinutes().toString());
        }

        if (isRemote) {
            params.append('tutoring_session[onlineMeetingUri]', onlineMeetingUri);
        } else {
            params.append('tutoring_session[building]', selectedBuilding? selectedBuilding.id: '');
            params.append('tutoring_session[room]', room);
        }

        fetch(Routing.generate(routeName, routeParams), {
                method: 'POST',
                body: params,
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
                    if (tutoringSession && updateTutoringSession) {
                        updateTutoringSession();
                    } else if (updateTutoring) {
                        updateTutoring();
                    }
                    toggleModal();
                }
            });
    }

    const onIsRemoteChange = (event: ChangeEvent<HTMLInputElement>) => {
        setIsRemote(event.target.value === 'isRemote' ?? false);
    }

    return <>
        {ready ?
            <>
                <GeneralErrorsRenderer errors={errors.generalErrors} />
                <div className='d-flex flex-column flex-lg-row'>
                    <div className='multiple-session-creation-info flex-1'>
                        <div className='hours line'>
                            <div className='label'>
                                {t('form.session')}
                            </div>
                            <div className={'d-flex justify-content-between' + (errors.endDateTime? ' invalid': '')}>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.from')} </span>
                                    <div className='start-time-value pe-2'>
                                        <DateTimePicker
                                            value={startDateTime}
                                            onChange={(date: Value) => onDateTimeChange(date, true)}
                                            disableClock
                                            calendarIcon={<FontAwesomeIcon className='text-secondary' icon="calendar-days" />}
                                            clearIcon={null}
                                        />
                                    </div>
                                </div>
                                <div className='d-flex align-items-center flex-grow-1'>
                                    <span className='pe-2'>{t('form.to')}</span>
                                    <div className='end-time-value'>
                                        <DateTimePicker
                                            value={endDateTime}
                                            onChange={(date: Value) => onDateTimeChange(date, false)}
                                            disableClock
                                            calendarIcon={<FontAwesomeIcon className='text-secondary' icon="calendar-days" />}
                                            clearIcon={null}
                                        />
                                    </div>
                                </div>
                            </div>

                            <Form.Control
                                hidden={true}
                                isInvalid={!!errors.endDateTime}
                            />
                            <Form.Control.Feedback className='mt-2' type='invalid'>
                                {errors.endDateTime}
                            </Form.Control.Feedback>
                        </div>
                        <div className='d-flex mb-3'>
                            <Form.Check
                                inline
                                label={t('form.on_site')}
                                type='radio'
                                name='isRemote'
                                value='isNotRemote'
                                checked={!isRemote}
                                onChange={onIsRemoteChange}
                            />
                            <Form.Check
                                inline
                                label={t('form.remote')}
                                type='radio'
                                name='isRemote'
                                value='isRemote'
                                checked={isRemote}
                                onChange={onIsRemoteChange}
                            />
                        </div>
                        {isRemote?
                            <div className='visio-link line'>
                                <div className='visio-link-label label'>
                                    {t('form.online_meeting_uri')}
                                </div>
                                <Form.Control
                                    className='visio-link-value flex-grow-1'
                                    value={onlineMeetingUri}
                                    onChange={(event) => {
                                            setOnlineMeetingUri(event.target.value);
                                            removeError('onlineMeetingUri');
                                        }
                                    }
                                    isInvalid={!!errors.onlineMeetingUri}
                                />
                                <Form.Control.Feedback className='mt-2' type='invalid'>
                                    {errors.onlineMeetingUri}
                                </Form.Control.Feedback>
                            </div>
                            :
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
                                        }}
                                        isInvalid={!!errors.room}
                                    />
                                    <Form.Control.Feedback className='mt-2' type='invalid'>
                                        {errors.room}
                                    </Form.Control.Feedback>
                                </div>
                            </div>
                        }
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
                        {tutoringSession? t('form.save_button') :t('form.create_button')}
                    </Button>
                </div>
            </>
            :
            <div>
                Loading ...
            </div>
        }
    </>;
};
