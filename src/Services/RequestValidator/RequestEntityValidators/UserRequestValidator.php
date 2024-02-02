<?php

namespace App\Services\RequestValidator\RequestEntityValidators;

use App\Entity\User;
use App\Exceptions\InvalidRequestException;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use Symfony\Component\HttpFoundation\Request;

class UserRequestValidator extends RequestValidatorService
{
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
        $data = $this->decodeRequestContent($request);

        if (!isset($data['email'], $data['password'], $data['roles']) && !preg_match('/\/user\/update\/\d+/',
                $request->getPathInfo())) {
            $errors[] = [
                'field' => 'request',
                'message' => 'Request must contain email, password and roles fields',
            ];
            $this->showErrors($errors);
        }

        $errors = $this->verifyDataAgainstObject($data, $user);

        $this->checkErrorsAndThrow($errors);
    }
}