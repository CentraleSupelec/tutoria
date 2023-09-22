import React from 'react';
import { useState } from 'react';
import { Button, Modal, Tab, Tabs } from 'react-bootstrap';
import Campus from '../../interfaces/Campus';
import Tutoring from '../../interfaces/Tutoring';
import { useTranslation } from 'react-i18next';
import TutoringSessionModalContent from './Content/TutoringSessionModalContent';
import BatchTutoringSessionCreationModalContent from './Content/BatchTutoringSessionCreationModalContent';

interface TutoringSessionCreationModalProps {
    tutoring: Tutoring,
    campuses: Campus[],
    onUpdate: Function,
}

export default function ({tutoring, campuses, onUpdate} : TutoringSessionCreationModalProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        <Button variant='secondary' onClick={toggleModal}>
            <i className="fa fa-solid fa-circle-plus" />
        </Button>

        <Modal className='session-creation-modal' show={isModalOpen} onHide={toggleModal} size='lg'>
            <Modal.Header closeButton closeLabel='Enregistrer'>
                <Modal.Title className='label'>
                    {t('form.create_single_or_batch_sessions')}
                </Modal.Title>
            </Modal.Header>
            {tutoring?
                <Modal.Body>
                    <Tabs>
                        <Tab eventKey='single' title={t('form.create_single_session')}>
                            <TutoringSessionModalContent tutoring={tutoring} campuses={campuses} toggleModal={toggleModal} updateTutoring={onUpdate}/>
                        </Tab>
                        <Tab eventKey='batch' title={t('form.batch_create_sessions')}>
                            <BatchTutoringSessionCreationModalContent
                                tutoring={tutoring}
                                campuses={campuses}
                                toggleModal={toggleModal}
                                saveTutoring={false}
                                onUpdate={onUpdate}
                            />
                        </Tab>
                    </Tabs>
                </Modal.Body>
            : null
            }
        </Modal>
    </>;
}
