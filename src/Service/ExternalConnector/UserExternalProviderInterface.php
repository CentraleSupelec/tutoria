<?php

namespace App\Service\ExternalConnector;

use App\Entity\Student;
use App\Exception\ExternalConnector\ExternalConnectorException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

interface UserExternalProviderInterface
{
    /**
     * @throws ExternalConnectorException
     */
    public function createOrUpdateUserFromExternalSourceByEmail(string $email);

    /**
     * @throws ExternalConnectorException
     */
    public function createOrUpdateUserFromExternalSourceByExternalSourceId(string $externalSourceId, array $externalSourceUser = null);

    /**
     * @throws ExternalConnectorException|UnexpectedTypeException
     */
    public function refreshUserFromExternalSource(Student $student);
}
