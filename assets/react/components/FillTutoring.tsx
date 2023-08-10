import React from 'react';
import { useState } from 'react';
import { Button, Modal} from 'react-bootstrap';
import BatchTutoringSessionCreationModalContent from './BatchTutoringSessionCreationModalContent';
import Campus from '../interfaces/Campus';
import Tutoring from '../interfaces/Tutoring';
import { useTranslation } from 'react-i18next';

interface FillTutoringProps {
    tutoring: Tutoring,
    campuses: Campus[],
}

export default function ({tutoring, campuses} : FillTutoringProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);
    
    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        <Button variant="success" onClick={toggleModal}>
            {t('form.batch_create_title')}
        </Button>
        
        <Modal className='session-creation-modal' show={isModalOpen} onHide={toggleModal} size='lg'>
            <Modal.Header closeButton closeLabel='Enregistrer'>
                <Modal.Title className='modal-title'>
                    {t('form.batch_create_title')}
                </Modal.Title>
            </Modal.Header>
            {tutoring?
                <Modal.Body>
                    <BatchTutoringSessionCreationModalContent tutoring={tutoring} campuses={campuses} toggleModal={toggleModal}/>
                </Modal.Body>
            : null
            }
        </Modal>
    </>;
}
