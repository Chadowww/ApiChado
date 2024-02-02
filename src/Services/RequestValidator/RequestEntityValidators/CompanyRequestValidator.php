<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\Company;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class CompanyRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['name'], $data['phone'], $data['address'], $data['city'], $data['country'], $data['siret'], $data['userId'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain name, phone, address, city, country, siret and userId fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $company);

        $this->checkErrorsAndThrow($errors);
    }
}