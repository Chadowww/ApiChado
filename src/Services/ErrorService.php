<?php

namespace App\Services;

use App\Entity\JobOffer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getErrors(JobOffer $jobOffer): array
    {
       $jobOfferErrors = $this->validator->validate($jobOffer);

         $errors = [];
        foreach ($jobOfferErrors as $jobOfferError) {
            if (!isset($errors[$jobOfferError->getPropertyPath()])){
                $errors['errors'][] = [
                    'field' => $jobOfferError->getPropertyPath(),
                    'message' => $jobOfferError->getMessage()
                ];
            }
        }
        if ($errors) {
           return $errors;
        }
        return [];
    }
}