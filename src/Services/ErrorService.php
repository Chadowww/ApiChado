<?php

namespace App\Services;

use App\Entity\Contract;
use App\Entity\JobOffer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getErrorsJobOfferRequest(Request $request): array
    {
        $errors = [];
        $data = $request->isMethod('put') ? $request->query->all() : $request->request->all();

        foreach ($data as $key => $value) {
            if (($key === 'title' || $key === 'description' || $key === 'city') && empty($value)) {
                $errors['errors'][] = [
                    'field' => $key,
                    'message' => 'This value should not be blank.'
                ];
            }

            if (($key === 'salaryMin' || $key === 'salaryMax') && !is_numeric($value)) {
                $errors['errors'][] = [
                    'field' => $key,
                    'message' => 'This value should be numeric.'
                ];
            }
        }

        return $errors;
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