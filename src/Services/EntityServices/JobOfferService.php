<?php

namespace App\Services\EntityServices;

use App\Entity\JobOffer;
use Symfony\Component\HttpFoundation\Request;

class JobOfferService {

    public function buildJobOffer(Request $request): JobOffer
    {
        $jobOffer = new JobOffer();
        $jobOffer->setTitle($request->get('title'));
        $jobOffer->setDescription($request->get('description'));
        $jobOffer->setCity($request->get('city'));
        $jobOffer->setSalaryMin($request->get('salaryMin'));
        $jobOffer->setSalaryMax($request->get('salaryMax'));
        return $jobOffer;
    }

    public function updateJobOffer(JobOffer|bool $jobOffer, Request $request): JobOffer|bool
    {
        if ($jobOffer instanceof JobOffer) {
            $jobOffer->setTitle($request->get('title'));
            $jobOffer->setDescription($request->get('description'));
            $jobOffer->setCity($request->get('city'));
            $jobOffer->setSalaryMin($request->get('salaryMin'));
            $jobOffer->setSalaryMax($request->get('salaryMax'));
        }
        return $jobOffer;
    }
}