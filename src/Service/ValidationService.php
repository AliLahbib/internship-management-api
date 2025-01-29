<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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