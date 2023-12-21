<?php

namespace App\Services\EntityServices;

use App\Entity\Company;
use Symfony\Component\HttpFoundation\Request;

class CompanyService
{

    /**
     * @throws \JsonException
     */
    public function buildCompany(Request $request): Company
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $company = new Company();
        $company->setName($data['name']);
        $company->setPhone($data['phone']);
        $company->setAddress($data['address']);
        $company->setCity($data['city']);
        $company->setCountry($data['country']);
        $company->setSiret($data['siret']);
        $company->setSlug(str_ireplace(' ', '-', $data['name']));
        $company->setUserId($data['user_id']);

        return $company;
    }
}