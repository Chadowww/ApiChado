<?php

namespace App\Services;

use App\Entity\Apply;
use App\Entity\Candidate;
use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\JobOffer;
use App\Entity\Resume;
use App\Entity\User;
use App\Exceptions\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Service to handle errors in requests before they are processed
 */
class ErrorService
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
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
     * @throws InvalidRequestException
     */
    Public function getErrorsContractRequest(Request $request): void
    {
        $errors = [];
        $contract = new Contract();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['type'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain type field',
            ];
        }

        foreach ($data as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($contract, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(Contract::class, $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }

    /**
     * @throws \JsonException
     * @throws InvalidRequestException
     */
    public function getErrorsUserRequest(Request $request): void
    {
        $errors = [];
        $user = new User();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['email'], $data['password'], $data['roles']) && !preg_match('/\/user\/update\/\d+/',
                $request->getPathInfo())) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain email, password and roles fields',
            ];
        }

        foreach ($data as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($user, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(User::class, $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }

    /**
     * @throws \JsonException
     * @throws InvalidRequestException
     */
    public function getErrorsCandidateRequest(Request $request): void
    {
        $errors = [];
        $candidate = new Candidate();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['firstname'], $data['lastname'], $data['userId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain firstname, lastname, userId fields',
            ];
        }

        foreach ($data as $key => $value){
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($candidate, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(Candidate::class, $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }

        }

        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }

    /**
     * @throws \JsonException
     * @throws InvalidRequestException
     */
    public function getErrorsCompanyRequest(Request $request): void
    {
        $errors = [];
        $company = new Company();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['name'], $data['phone'], $data['address'], $data['city'], $data['country'], $data['siret'], $data['userId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain name, phone, address, city, country, siret and userId fields',
            ];
        }

        foreach ($data as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($company, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(Company::class, $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }

    /**
     * @throws \JsonException
     * @throws InvalidRequestException
     */
    public function getErrorsResumeRequest(Request $request): void
    {
        $errors = [];
        $resume = new Resume();
        $data = $request->request->all();

        if (!isset($data['title'], $data['candidateId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain title and candidateId fields',
            ];
        }

        foreach ($data as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($resume, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(Resume::class, $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }

    /**
     * @throws \JsonException
     * @throws InvalidRequestException
     */
    public function getErrorsApplyRequest(Request $request): void
    {
        $errors = [];
        $apply = new Apply();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['status'], $data['candidateId'],$data['resumeId'], $data['jobofferId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain status, candidateId, resumeId and jobofferId fields',
            ];
        }

        foreach ($data as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($apply, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(Apply::class, $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }
}