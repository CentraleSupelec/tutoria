<?php

namespace App\Utils;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Validator\ConstraintViolation;

class ErrorUtils
{
    public function parseFormErrors(FormErrorIterator $formErrors): array
    {
        $errorsList = [];

        foreach ($formErrors as $formError) {
            /** @var ConstraintViolation $cause */
            $cause = $formError->getCause();

            // $cause->getPropertyPath() return 'data' if the error is at the root of the entity
            // or 'data.fieldName' if the error is on a certain field of the entity
            $errorsList[] = [
                'message' => $cause->getMessage(),
                'propertyPath' => $cause->getPropertyPath(),
            ];
        }

        return $errorsList;
    }
}
