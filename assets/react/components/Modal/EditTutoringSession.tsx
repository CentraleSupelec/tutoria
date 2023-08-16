import React from 'react';
import { useState } from 'react';
import { Modal } from 'react-bootstrap';
import Campus from '../../interfaces/Campus';
import Tutoring from '../../interfaces/Tutoring';
import { useTranslation } from 'react-i18next';
import TutoringSessionModalContent from './Content/TutoringSessionModalContent';
import TutoringSession from '../../interfaces/TutoringSession';

interface EditTutoringProps {
    tutoring: Tutoring,
    tutoringSession: TutoringSession
    campuses: Campus[],
    updateTutoringSession: Function
}

export default function ({tutoring, tutoringSession, campuses, updateTutoringSession} : EditTutoringProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        <div className='interactive-button-container' onClick={toggleModal} style={{cursor: 'pointer'}}>
            <i className="fa-lg fa-solid fa-pen-to-square p-2" style={{color: '#00807a'}} />
        </div>

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
