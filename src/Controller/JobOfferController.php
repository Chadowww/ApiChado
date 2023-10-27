<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JobOfferController extends AbstractController
{
    private JobOfferRepository $jobOfferRepository;
    private SerializerInterface $serializer;
    private ErrorService $errorService;

    public function __construct(
        JobOfferRepository $jobOfferRepository,
        SerializerInterface $serializer,
        ErrorService $errorService,
    )
    {
        $this->jobOfferRepository = $jobOfferRepository;
        $this->serializer = $serializer;
        $this->errorService = $errorService;
    }

    public function create(Request $request): JsonResponse
    {
        $jobOffer = new JobOffer();
        $jobOffer->setTitle($request->get('title'));
        $jobOffer->setDescription($request->get('description'));
        $jobOffer->setCity($request->get('city'));
        $jobOffer->setSalaryMin($request->get('salaryMin'));
        $jobOffer->setSalaryMax($request->get('salaryMax'));

        if ($this->errorService->getErrors($jobOffer) === []) {
            $this->jobOfferRepository->create($jobOffer);
            return new JsonResponse('Created', 201);
        }

        return new JsonResponse($this->errorService->getErrors($jobOffer), 400);
    }

    public function read(int $id): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);

        if(!$jobOffer) {
            return new JsonResponse('id not found', 404);
        }

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

        if ($this->errorService->getErrors($jobOffer) === []) {
            $this->jobOfferRepository->create($jobOffer);
            return new JsonResponse('Updated', 201);
        }

        return new JsonResponse($this->errorService->getErrors($jobOffer), 400);
    }

    public function delete(int $id): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);

        if (!$jobOffer){
            return new JsonResponse('id not found', 404);
        }
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