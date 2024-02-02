<?php

namespace App\Services\RequestValidator\RequestValidatorService;

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

class RequestValidatorService
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
     * Decode request content
     * @param Request $request
     * @return array
     * @throws \JsonException
     */
    protected function decodeRequestContent(Request $request): array
    {
        return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * show errors if there are any
     * @param array $errors
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    protected function checkErrorsAndThrow(array $errors): void
    {
        if (count($errors) > 0) {
            $this->showErrors($errors);
        }
    }

    /**
     * @param array $errors
     * error to show
     * @return void
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    protected function showErrors(array $errors,): void
    {
        throw new InvalidRequestException(json_encode($errors, JSON_THROW_ON_ERROR), 400);
    }

    /**
     * @param array $data
     * array to verify
     * @param JobOffer|Contract|User|Candidate|Company|Resume|Apply $object
     * object to verify against
     * @return array
     */
    protected function verifyDataAgainstObject(
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