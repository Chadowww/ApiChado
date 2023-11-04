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
        switch ($data){
            case $data === []:
                $errors[] = [
                    'field' => 'body',
                    'message' => 'Request body is empty'
                ];
                break;
            case $this->validator->validatePropertyValue(JobOffer::class, 'title', $data['title'])->count() > 0:
                $errors[] = [
                    'field' => 'title',
                    'message' => $this->validator->validatePropertyValue(JobOffer::class, 'title', $data['title'])
                        ->get(0)->getMessage(),
                    'passedValue' => $data['title']
                ];
                break;
            case $this->validator->validatePropertyValue(JobOffer::class, 'description', $data['description'])->count() > 0:
                $errors[] = [
                    'field' => 'description',
                    'message' => $this->validator->validatePropertyValue(JobOffer::class, 'description', $data['description'])->get(0)->getMessage(),
                    'passedValue' => $data['description']
                ];
                break;
            case $this->validator->validatePropertyValue(JobOffer::class, 'city', $data['city'])->count() > 0:
                $errors[] = [
                    'field' => 'city',
                    'message' => $this->validator->validatePropertyValue(JobOffer::class, 'city', $data['city'])->get
                    (0)->getMessage(),
                    'passedValue' => $data['city']
                ];
                break;
            case $this->validator->validatePropertyValue(JobOffer::class, 'salaryMin', (int)$data['salaryMin'])->count() > 0:
                $errors[] = [
                    'field' => 'salaryMin',
                    'message' => $this->validator->validatePropertyValue(JobOffer::class, 'salaryMin', $data['salaryMin'])->get(0)->getMessage(),
                    'passedValue' => $data['salaryMin']
                ];
                break;
            case $this->validator->validatePropertyValue(JobOffer::class, 'salaryMax', (int)$data['salaryMax'])->count() > 0:
                $errors[] = [
                    'field' => 'salaryMax',
                    'message' => $this->validator->validatePropertyValue(JobOffer::class, 'salaryMax', $data['salaryMax'])->get(0)->getMessage(),
                    'passedValue' => $data['salaryMax']
                ];
                break;
        }

        return $errors;
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