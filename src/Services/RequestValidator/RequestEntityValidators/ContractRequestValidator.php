<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\Contract;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class ContractRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['type'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain type field',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $contract);

        $this->checkErrorsAndThrow($errors);
    }

}