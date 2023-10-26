<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class JobOfferController extends AbstractController
{
    public function __construct(
        JobOfferRepository $jobOfferRepository,
        SerializerInterface $serializer,
    )
    {
        $this->jobOfferRepository = $jobOfferRepository;
        $this->serializer = $serializer;
    }

//    public function index()
//    {
//        $jobOffers = $this->jobOfferRepository->list();
//        return $this->json($jobOffers);
//    }

    public function create(): JsonResponse
    {
        $jobOffer = new JobOffer();
        $jobOffer->setTitle('Développeur PHP');
        $jobOffer->setDescription('Développeur PHP/Symfony junior qui roxxe du poney');
        $jobOffer->setCity('Paris');
        $jobOffer->setSalaryMin(30000);
        $jobOffer->setSalaryMax(40000);

        if ($this->jobOfferRepository->create($jobOffer)) {
            return new JsonResponse('Created', 201);
        }
        return new JsonResponse('Error', 500);
    }

    public function read(int $id)
    {
        $jobOffer = $this->jobOfferRepository->read($id);
        $jobOfferJson = $this->serializer->serialize($jobOffer, 'json');

        return new JsonResponse($jobOfferJson, 200, [], true);
    }

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