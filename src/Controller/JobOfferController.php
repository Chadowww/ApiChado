<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JobOfferController extends AbstractController
{
    public function __construct(JobOfferRepository $jobOfferRepository)
    {
        $this->jobOfferRepository = $jobOfferRepository;
    }

//    public function index()
//    {
//        $jobOffers = $this->jobOfferRepository->list();
//        return $this->json($jobOffers);
//    }

    public function create()
    {
        $jobOffer = new JobOffer();
        $jobOffer->setTitle('Développeur PHP');
        $jobOffer->setDescription('Développeur PHP/Symfony junior qui roxxe du poney');
        $jobOffer->setCity('Paris');
        $jobOffer->setSalaryMin(30000);
        $jobOffer->setSalaryMax(40000);

        if ($this->jobOfferRepository->create($jobOffer)) {
            return $this->json($jobOffer);
        }
    }

//    public function read(int $id)
//    {
//        $jobOffer = $this->jobOfferRepository->read($id);
//        return $this->json($jobOffer);
//    }

//    public function update(int $id)
//    {
//        $this->jobOfferRepository->update($id);
//    }

//    public function delete(int $id)
//    {
//        $jobOffer = $this->jobOfferRepository->read($id);
//        $this->jobOfferRepository->delete($jobOffer);
//
//    }
}