<?php

namespace App\Tests;

use App\Entity\JobOffer;
use App\Services\EntityServices\JobOfferService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JobOfferServiceTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testUpdateJobOffer(): void
    {
        $jobOffer = new JobOffer();
        $jobOffer->setTitle('Job Offer');
        $jobOffer->setDescription('Job Offer Description');
        $jobOffer->setCity('New York');
        $jobOffer->setSalaryMax(100000);
        $jobOffer->setSalaryMin(50000);

        $data = [
            'title' => 'New Job Offer',
            'description' => 'New description',
            'city' => 'Toronto',
            'salaryMax' => 90000,
            'salaryMin' => 80000
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $jobOfferService = new JobOfferService();
        $jobOfferService->updateJobOffer($jobOffer, $request);

        $this->assertEquals($jobOffer->getTitle(), $data['title']);
        $this->assertEquals($jobOffer->getDescription(), $data['description']);
        $this->assertEquals($jobOffer->getCity(), $data['city']);
        $this->assertEquals($jobOffer->getSalaryMax(), $data['salaryMax']);
        $this->assertEquals($jobOffer->getSalaryMin(), $data['salaryMin']);
    }

    /**
     * @throws \JsonException
     */
    public function testCreateJobOffer(): void
    {
        $data = [
            'title' => 'Job Offer',
            'description' => 'Job Offer Description',
            'city' => 'New York',
            'salaryMax' => 100000,
            'salaryMin' => 50000
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $jobOfferService = new JobOfferService();

        $jobOffer = $jobOfferService->buildJobOffer($request);

        $this->assertEquals($jobOffer->getTitle(), $data['title']);
        $this->assertEquals($jobOffer->getDescription(), $data['description']);
        $this->assertEquals($jobOffer->getCity(), $data['city']);
        $this->assertEquals($jobOffer->getSalaryMax(), $data['salaryMax']);
        $this->assertEquals($jobOffer->getSalaryMin(), $data['salaryMin']);
    }
}
