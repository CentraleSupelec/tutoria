import React from 'react';
import { useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';

interface DeleteConfirmationProps {
    onConfirmDelete: Function
}

export default function ({onConfirmDelete} : DeleteConfirmationProps) {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const toggleModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    return <>
        <div className='interactive-button-container ms-3' onClick={toggleModal} style={{cursor: 'pointer'}}>
            <i className="text-secondary fa-lg fa-solid fa-trash-can p-2" />
        </div>

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
                <Button variant='primary' onClick={toggleModal}>
                    {t('confirmation_modal.cancel')}
                </Button>
                <Button variant='secondary' onClick={() => onConfirmDelete()}>
                    {t('confirmation_modal.delete')}
                </Button>
            </Modal.Footer>
        </Modal>
    </>;
}
