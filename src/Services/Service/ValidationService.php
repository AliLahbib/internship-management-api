<?php

namespace App\Services\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    public function validate($entity): void
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new BadRequestHttpException(json_encode($messages));
        }
    }
} 