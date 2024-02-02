<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\Resume;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class ResumeRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['title'], $data['candidateId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain title and candidateId fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $resume);

        $this->checkErrorsAndThrow($errors);
    }
}