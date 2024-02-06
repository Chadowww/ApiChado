<?php

namespace App\Services\RequestValidator;

use App\Entity\{Apply,Candidate, Company, Contract, JobOffer, Resume, User};
use App\Exceptions\InvalidRequestException;
use ReflectionClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     * @param JobOffer|Contract|User|Candidate|Company|Resume|Apply $object
     * @return array
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function throwError400FromData(
        array $data,
        JobOffer | Contract | User | Candidate | Company | Resume | Apply $object
    ): void
    {
        $errors = [];
        $requiredFields = $this->getRequiredFields($object);

        if (count($requiredFields) > 0) {
            $errors[] = [
                'field' => 'request body',
                'message' => 'The request must contain the following fields:',
                'missingFields' => ''
            ];
            foreach ($requiredFields as $requiredField) {
                $errors[0]['message'] .= $requiredField . ', ';
                if (!array_key_exists($requiredField, $data)) {
                    $errors[0]['missingFields'] .= $requiredField . ', ';
                }
            }
            if (!empty($errors[0]['missingFields'])) {
                $errors[0]['missingFields'] = rtrim($errors[0]['missingFields'], ', ');
                throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
            }

            $errors = [];
        }

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
        if (count($errors) > 0) {
            throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
        }
    }

    /**
     * @param JobOffer|Contract|Candidate|Resume|Apply|Company|User $object
     * @return array
     */
    private function getRequiredFields(JobOffer|Contract|Candidate|Resume|Apply|Company|User $object): array
    {
        $requiredFields = [];

        $class = new ReflectionClass($object);
        $properties = $class->getProperties();

        foreach($properties as $property) {
            $attributes = $property->getAttributes(NotBlank::class);
            if (!empty($attributes)) {
                $requiredFields[] = $property->getName();
            }
        }
        return $requiredFields;
    }
}