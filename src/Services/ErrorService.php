<?php

namespace App\Services;

use App\Entity\Contract;
use App\Entity\JobOffer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getErrorsJobOffer(JobOffer $jobOffer): array
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

    Public function getErrorsContract(Contract $contract): array
    {
        $contractErrors = $this->validator->validate($contract);

        $errors = [];
        foreach ($contractErrors as $contractError) {
            if (!isset($errors[$contractError->getPropertyPath()])){
                $errors['errors'][] = [
                    'field' => $contractError->getPropertyPath(),
                    'message' => $contractError->getMessage()
                ];
            }
        }
        if ($errors) {
            return $errors;
        }
        return [];
    }
}