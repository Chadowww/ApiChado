<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\Candidate;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class CandidateRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['firstname'], $data['lastname'], $data['userId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain firstname, lastname, userId fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $candidate);

        $this->checkErrorsAndThrow($errors);
    }
}