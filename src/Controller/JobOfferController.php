<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JobOfferController extends AbstractController
{
    private JobOfferRepository $jobOfferRepository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        JobOfferRepository $jobOfferRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )
    {
        $this->jobOfferRepository = $jobOfferRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function create(Request $request): JsonResponse
    {
        $jobOffer = new JobOffer();
        $jobOffer->setTitle($request->get('title'));
        $jobOffer->setDescription($request->get('description'));
        $jobOffer->setCity($request->get('city'));
        $jobOffer->setSalaryMin($request->get('salaryMin'));
        $jobOffer->setSalaryMax($request->get('salaryMax'));

        if (empty($this->validator->validate($jobOffer))) {
            return new JsonResponse('Created', 201);
        }
        return new JsonResponse('Error', 500);
    }

    public function read(int $id): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);
        $jobOfferJson = $this->serializer->serialize($jobOffer, 'json');

        return new JsonResponse($jobOfferJson, 200, [], true);
    }

    public function update(int $id, Request$request): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);
        $jobOffer->setTitle($request->get('title'));
        $jobOffer->setDescription($request->get('description'));
        $jobOffer->setCity($request->get('city'));
        $jobOffer->setSalaryMin($request->get('salaryMin'));
        $jobOffer->setSalaryMax($request->get('salaryMax'));

        if (empty($this->validator->validate($jobOffer))) {
            return new JsonResponse('Updated', 200);
        }
        return new JsonResponse('Error', 500);
    }

    public function delete(int $id): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);
        if ($this->jobOfferRepository->delete($jobOffer)) {
            return new JsonResponse('Deleted', 200);
        }
        return new JsonResponse('Error', 500);
    }

        public function list(): JsonResponse
        {
        $jobOffers = $this->jobOfferRepository->list();
        if ($jobOffers) {
            $jobOffersJson = $this->serializer->serialize($jobOffers, 'json');
            return new JsonResponse($jobOffersJson, 200, [], true);
        }
        return new JsonResponse('Error', 500);

    }
}