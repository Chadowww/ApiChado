<?php

namespace App\Services;

use App\Entity\Candidate;
use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\JobOffer;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws \JsonException
     */
    public function getErrorsJobOfferRequest(Request $request): array
    {
        $errors = [];
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

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

    /**
     * @throws \JsonException
     */
    Public function getErrorsContractRequest(Request $request): array
    {
        $errors = [];
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
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

    /**
     * @throws \JsonException
     */
    public function getErrorsUserRequest(Request $request): array
    {
        $errors = [];
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['email'], $data['password'], $data['roles'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain email, password and roles fields',
            ];
        }
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'email':
                    if ($this->validator->validatePropertyValue(User::class, 'email', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'email',
                            'message' => $this->validator->validatePropertyValue(User::class, 'email', $value)->get(0)
                                ->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case 'password':
                    if ($this->validator->validatePropertyValue(User::class, 'password', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'password',
                            'message' => $this->validator->validatePropertyValue(User::class, 'password', $value)->get
                            (0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case 'roles':
                    if ($this->validator->validatePropertyValue(User::class, 'roles', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'roles',
                            'message' => $this->validator->validatePropertyValue(User::class, 'roles', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
            }
        }
        return $errors;
    }

    /**
     * @throws \JsonException
     */
    public function getErrorsCandidateRequest(Request $request): array
    {
        $errors = [];
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['firstname'], $data['lastname'], $data['user_id'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain firstname, lastname, user_id fields',
            ];
        }

        foreach ($data as $key => $value){
            switch ($key) {
                case  'firstname':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'firstname', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'firstname',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'firstname', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'lastname':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'lastname', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'lastname',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'lastname', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'user_id':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'user_id', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'user_id',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'user_id', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'phone':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'phone', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'phone',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'phone', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'address':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'address', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'address',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'address', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'city':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'city', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'city',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'city', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'country':
                    if ($this->validator->validatePropertyValue(Candidate::class, 'country', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'country',
                            'message' => $this->validator->validatePropertyValue(Candidate::class, 'country', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
            }
        }

        return $errors;
    }


    /**
     * @throws \JsonException
     */
    public function getErrorsCompanyRequest(Request $request): array
    {
        $errors = [];
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['name'], $data['phone'], $data['address'], $data['city'], $data['country'], $data['siret'], $data['user_id'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain name, phone, address, city, country, siret and user_id fields',
            ];
        }

        foreach ($data as $key => $value) {
            switch ($key) {
                case  'name':
                    if ($this->validator->validatePropertyValue(Company::class, 'name', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'name',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'name', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'phone':
                    if ($this->validator->validatePropertyValue(Company::class, 'phone', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'phone',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'phone', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'address':
                    if ($this->validator->validatePropertyValue(Company::class, 'address', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'address',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'address', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'city':
                    if ($this->validator->validatePropertyValue(Company::class, 'city', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'city',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'city', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'country':
                    if ($this->validator->validatePropertyValue(Company::class, 'country', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'country',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'country', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'siret':
                    if ($this->validator->validatePropertyValue(Company::class, 'siret', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'siret',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'siret', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
                case  'user_id':
                    if ($this->validator->validatePropertyValue(Company::class, 'user_id', $value)->count() > 0) {
                        $errors[] = [
                            'field' => 'user_id',
                            'message' => $this->validator->validatePropertyValue(Company::class, 'user_id', $value)->get(0)->getMessage(),
                            'passedValue' => $value
                        ];
                    }
                    break;
            }
        }

        return $errors;
    }
}