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

        if (!isset($data['title'], $data['description'], $data['city'], $data['salaryMin'], $data['salaryMax'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain title, description, city, salaryMin and salaryMax fields',
            ];
        }

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'title':
                    if ($this->validator->validatePropertyValue(JobOffer::class, 'title', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'title',
                            'message' => $this->validator->validatePropertyValue(JobOffer::class, 'title', $value)
                                ->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case 'description':
                    if ($this->validator->validatePropertyValue(JobOffer::class, 'description', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'description',
                            'message' => $this->validator->validatePropertyValue(JobOffer::class, 'description', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case 'city':
                    if ($this->validator->validatePropertyValue(JobOffer::class, 'city', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'city',
                            'message' => $this->validator->validatePropertyValue(JobOffer::class, 'city', $value)->get
                            (0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case 'salaryMin':
                    if ($this->validator->validatePropertyValue(JobOffer::class, 'salaryMin', $value)->count() > 0 || !is_numeric($value)){
                        $errors[] = [
                            'field' => 'salaryMin',
                            'message' => $this->validator->validatePropertyValue(JobOffer::class, 'salaryMin', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case 'salaryMax':
                    if ($this->validator->validatePropertyValue(JobOffer::class, 'salaryMax', $value)->count() > 0 ||
                        !is_numeric($value)){
                        $errors[] = [
                            'field' => 'salaryMax',
                            'message' => $this->validator->validatePropertyValue(JobOffer::class, 'salaryMax', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
            }
        }
        return $errors;
    }

    Public function getErrorsContractRequest(Request $request): array
    {
        $errors = [];
        $data = $request->isMethod('put') ? $request->query->all() : $request->request->all();
        if (!isset($data['type'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain type field',
            ];
        }
        foreach ($data as $key => $value) {
            if (($key === 'type') && $this->validator->validatePropertyValue(Contract::class, 'type', $value)->count() > 0) {
                $errors[] = [
                    'field' => 'type',
                    'message' => $this->validator->validatePropertyValue(Contract::class, 'type', $value)->get(0)->getMessage(),
                    'passedValue' => $value
                ];
            }
        }
        return $errors;
    }
}