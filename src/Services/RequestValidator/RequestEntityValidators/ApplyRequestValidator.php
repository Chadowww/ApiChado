<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\Apply;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class ApplyRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['status'], $data['candidateId'],$data['resumeId'], $data['jobofferId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain status, candidateId, resumeId and jobofferId fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $apply);

        $this->checkErrorsAndThrow($errors);
    }
}