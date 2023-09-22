import React from 'react';
import { useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import Campus from '../../interfaces/Campus';
import Tutoring from '../../interfaces/Tutoring';
import { useTranslation } from 'react-i18next';
import TutoringSessionModalContent from './Content/TutoringSessionModalContent';
import TutoringSession from '../../interfaces/TutoringSession';

interface EditTutoringProps {
    tutoring: Tutoring,
    tutoringSession: TutoringSession
    campuses: Campus[],
    updateTutoringSession: Function,
    isTutoringSessionsEnded: boolean,
}

export default function ({tutoring, tutoringSession, campuses, updateTutoringSession, isTutoringSessionsEnded} : EditTutoringProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        <Button variant={isTutoringSessionsEnded?'outline-secondary':'secondary'} onClick={toggleModal} disabled={isTutoringSessionsEnded}>
            <i className="fa fa-solid fa-pen-to-square" />
        </Button>

        <Modal className='session-creation-modal' show={isModalOpen} onHide={toggleModal} size='lg'>
            <Modal.Header closeButton closeLabel='Enregistrer'>
                <Modal.Title className='label'>
                    {t('form.edit_tutoring_session')}
                </Modal.Title>
            </Modal.Header>
            {tutoringSession?
                <Modal.Body>
                    <TutoringSessionModalContent tutoring={tutoring} tutoringSession={tutoringSession} campuses={campuses} toggleModal={toggleModal} updateTutoringSession={updateTutoringSession}/>
                </Modal.Body>
            : null
            }
        </Modal>
    </>;
}
