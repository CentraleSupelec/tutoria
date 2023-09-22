import React from 'react';
import { useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';

interface DeleteConfirmationProps {
    onConfirmDelete: Function,
    isTutoringSessionsEnded: boolean,
}

export default function ({onConfirmDelete, isTutoringSessionsEnded} : DeleteConfirmationProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        <Button variant='outline-secondary' onClick={toggleModal} disabled={isTutoringSessionsEnded}>
            <i className="fa fa-solid fa-trash-can" />
        </Button>

        <Modal className='session-creation-modal' show={isModalOpen} onHide={toggleModal} size='lg'>
            <Modal.Header closeButton>
                <Modal.Title className='label'>
                    {t('confirmation_modal.title')}
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {t('confirmation_modal.body')}
            </Modal.Body>
            <Modal.Footer>
                <Button variant='outline-danger' onClick={toggleModal}>
                    {t('confirmation_modal.cancel')}
                </Button>
                <Button variant='danger' onClick={() => onConfirmDelete()}>
                    {t('confirmation_modal.delete')}
                </Button>
            </Modal.Footer>
        </Modal>
    </>;
}
