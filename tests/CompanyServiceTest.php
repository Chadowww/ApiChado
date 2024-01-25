<?php

namespace App\Tests;

use App\Entity\Company;
use App\Services\EntityServices\CompanyService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CompanyServiceTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testBuildCompany(): void
    {
        $data = [
            'name' => 'Company',
            'phone' => '123456789',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'siret' => '123456789',
            'user_id' => 1
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $companyService = new CompanyService();
        $company = $companyService->buildCompany($request);
        $this->assertEquals($company->getName(), $data['name']);
        $this->assertEquals($company->getPhone(), $data['phone']);
        $this->assertEquals($company->getAddress(), $data['address']);
        $this->assertEquals($company->getCity(), $data['city']);
        $this->assertEquals($company->getCountry(), $data['country']);
        $this->assertEquals($company->getSiret(), $data['siret']);
        $this->assertEquals($company->getUserId(), $data['user_id']);
        $this->assertEquals($company->getSlug(), str_ireplace(' ', '-', $data['name']));
    }

    /**
     * @throws \JsonException
     */
    public function testUpdateCompany(): void
    {
        $company = new Company();
        $company->setName('Company');
        $company->setPhone('1234567890');
        $company->setAddress('123 Main St');
        $company->setCity('New York');
        $company->setCountry('USA');
        $company->setSiret('12345678901234');
        $company->setUserId(1);
        $company->setSlug('Company');

        $data = [
            'name' => 'Entreprise',
            'phone' => '0987654321',
            'address' => '10 rue de la paix',
            'city' => 'Paris',
            'country' => 'France',
            'siret' => '12345678901234',
            'user_id' => 1,
            'slug' => 'Entreprise',
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $companyService = new CompanyService();
        $companyService->updateCompany($company, $request);
        $this->assertEquals($company->getName(), $data['name']);
        $this->assertEquals($company->getPhone(), $data['phone']);
        $this->assertEquals($company->getAddress(), $data['address']);
        $this->assertEquals($company->getCity(), $data['city']);
        $this->assertEquals($company->getCountry(), $data['country']);
        $this->assertEquals($company->getSiret(), $data['siret']);
        $this->assertEquals($company->getUserId(), $data['user_id']);
        $this->assertEquals($company->getSlug(), str_ireplace(' ', '-', $data['name']));
    }
}
