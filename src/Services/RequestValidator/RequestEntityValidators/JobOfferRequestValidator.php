<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\JobOffer;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class JobOfferRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['title'], $data['description'], $data['city'], $data['salaryMin'], $data['salaryMax'])) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain title, description, city, salaryMin and salaryMax fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $jobOffer);

        $this->checkErrorsAndThrow($errors);
    }

}