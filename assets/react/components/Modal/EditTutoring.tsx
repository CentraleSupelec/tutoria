import React, { useEffect } from 'react';
import { useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import TutoringModalContent from './../Modal/Content/TutoringModalContent';
import Campus from '../../interfaces/Campus';
import Tutoring from '../../interfaces/Tutoring';
import { useTranslation } from 'react-i18next';
import BatchTutoringSessionCreationModalContent from './Content/BatchTutoringSessionCreationModalContent';

interface EditTutoringProps {
    tutoring: Tutoring,
    campuses: Campus[],
    withBatchTutoringSessionCreation: boolean,
    onUpdate: Function
}

export default function ({ tutoring, campuses, withBatchTutoringSessionCreation, onUpdate }: EditTutoringProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        {withBatchTutoringSessionCreation ?
            <Button variant="success" onClick={toggleModal}>
                {t('form.batch_create_title')}
            </Button>
            :
            <div className='interactive-button-container' onClick={toggleModal} style={{ cursor: 'pointer' }}>
                <i className="fa-xl fa-solid fa-pen-to-square p-2" style={{ color: '#00807a' }} />
            </div>
        }

        <Modal className='session-creation-modal' show={isModalOpen} onHide={toggleModal} size='lg'>
            <Modal.Header closeButton closeLabel='Enregistrer'>
                <Modal.Title className='modal-title'>
                    {withBatchTutoringSessionCreation ? t('form.batch_create_title') : t('form.update_tutoring_title')}
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {withBatchTutoringSessionCreation ?
                    <BatchTutoringSessionCreationModalContent
                        tutoring={tutoring}
                        campuses={campuses}
                        toggleModal={toggleModal}
                        saveTutoring={true}
                        onUpdate={onUpdate}
                    />
                    :
                    <TutoringModalContent 
                        tutoring={tutoring}
                        campuses={campuses}
                        toggleModal={toggleModal}
                        onUpdate={onUpdate}
                    />
                }
            </Modal.Body>
        </Modal>
    </>;
}
