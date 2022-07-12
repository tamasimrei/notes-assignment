<?php

namespace App\Service;

use App\Entity\Tag;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEntityService
{

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @param object $entity
     * @return array
     */
    public function validate(object $entity): array
    {
        $validationErrors = [];
        $constraintViolations = $this->validator->validate($entity);

        foreach ($constraintViolations as $violation) {
            /** @var ConstraintViolation $violation */
            $validationErrors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $validationErrors;
    }
}
