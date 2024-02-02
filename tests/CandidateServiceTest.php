<?php

namespace App\Tests;

use App\Entity\Candidate;
use App\Services\EntityServices\CandidateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CandidateServiceTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testUpdateCandidate(): void
    {
        $candidate = new Candidate();
        $candidate->setFirstName('John');
        $candidate->setLastName('Doe');
        $candidate->setUserId(1);
        $candidate->setSlug('John', 'Doe');
        $candidate->setPhone('123456789');
        $candidate->setAddress('123 Main St');
        $candidate->setCity('New York');
        $candidate->setCountry('USA');

        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'userId' => 1,
            'phone' => '123456789',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $candidateService = new CandidateService();
        $candidateService->updateCandidate($candidate, $request);

        $this->assertEquals($candidate->getFirstName(), $data['firstname']);
        $this->assertEquals($candidate->getLastName(), $data['lastname']);
        $this->assertEquals($candidate->getUserId(), $data['userId']);
        $this->assertEquals($candidate->getPhone(), $data['phone']);
        $this->assertEquals($candidate->getAddress(), $data['address']);
        $this->assertEquals($candidate->getCity(), $data['city']);
        $this->assertEquals($candidate->getCountry(), $data['country']);
    }

    /**
     * @throws \JsonException
     */
    public function testBuildCandidate(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'userId' => 1,
            'phone' => '123456789',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $candidateService = new CandidateService();
        $candidate = $candidateService->BuildCandidate($request);

        $this->assertEquals($candidate->getFirstName(), $data['firstname']);
        $this->assertEquals($candidate->getLastName(), $data['lastname']);
        $this->assertEquals($candidate->getUserId(), $data['userId']);
        $this->assertEquals($candidate->getPhone(), $data['phone']);
        $this->assertEquals($candidate->getAddress(), $data['address']);
        $this->assertEquals($candidate->getCity(), $data['city']);
        $this->assertEquals($candidate->getCountry(), $data['country']);
    }
}