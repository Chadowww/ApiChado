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

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function getErrorsJobOfferRequest(Request $request): void
    {
        $errors = [];
        $jobOffer = new JobOffer();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['title'], $data['description'], $data['city'], $data['salaryMin'], $data['salaryMax'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain title, description, city, salaryMin and salaryMax fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $jobOffer);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
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
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $contract);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
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
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $user);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
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
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $candidate);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
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
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $company);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function getErrorsResumeRequest(Request $request): void
    {
        $errors = [];
        $resume = new Resume();
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['title'], $data['candidateId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain title and candidateId fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $resume);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
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
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $apply);

        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param array $errors
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    private function showErrors(array $errors,): void
    {
        throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
    }

    /**
     * @param array $data
     * @param JobOffer|Contract|User|Candidate|Company|Resume|Apply $object
     * @return array
     */
    private function verifyDataAgainstObject(
        array $data,
        JobOffer | Contract | User | Candidate | Company | Resume | Apply $object
    ): array
    {
        $errors = [];

        foreach ($data as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($object, $setterMethod)) {
                $validationErrors = $this->validator->validatePropertyValue(get_class($object), $key, $value);
                if ($validationErrors->count() > 0) {
                    $errors[] = [
                        'field' => $key,
                        'message' => $validationErrors->get(0)->getMessage(),
                        'passedValue' => $value
                    ];
                }
            }
        }
        return $errors;
    }

}