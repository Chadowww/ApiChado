<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

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
        if ($this->errorService->getErrorsJobOfferRequest($request) !== []) {
            return new JsonResponse($this->errorService->getErrorsJobOfferRequest($request), 400);
        }
        $jobOffer = new JobOffer();
        $jobOffer->setTitle($request->get('title'));
        $jobOffer->setDescription($request->get('description'));
        $jobOffer->setCity($request->get('city'));
        $jobOffer->setSalaryMin($request->get('salaryMin'));
        $jobOffer->setSalaryMax($request->get('salaryMax'));

        if ($this->errorService->getErrorsJobOffer($jobOffer) === []) {
            try {
                $this->jobOfferRepository->create($jobOffer);
            } catch (\PDOException $e) {
                return new JsonResponse($e->getMessage(), 500);
            }
            return new JsonResponse('Created', 201);
        }

        return new JsonResponse($this->errorService->getErrorsJobOffer($jobOffer), 400);
    }

    public function read(int $id): JsonResponse
    {
        try {
            $jobOffer = $this->jobOfferRepository->read($id);
        } catch (\PDOException $e) {
            return new JsonResponse($e->getMessage(), 500);
        }

        if(!$jobOffer) {
            return new JsonResponse('id ' . $id . ' not found', 404);
        }

        $jobOfferJson = $this->serializer->serialize($jobOffer, 'json');
        return new JsonResponse($jobOfferJson, 200, [], true);
    }

    public function update(int $id, Request$request): JsonResponse
    {

        if ($this->errorService->getErrorsJobOfferRequest($request) !== []) {
            return new JsonResponse($this->errorService->getErrorsJobOfferRequest($request), 400);
        }
        $jobOffer = $this->jobOfferRepository->read($id);

       if($jobOffer){
           $jobOffer->setTitle($request->get('title'));
           $jobOffer->setDescription($request->get('description'));
           $jobOffer->setCity($request->get('city'));
           $jobOffer->setSalaryMin($request->get('salaryMin'));
           $jobOffer->setSalaryMax($request->get('salaryMax'));
       }

        if ($this->errorService->getErrorsJobOffer($jobOffer) === []) {
            try {
                $this->jobOfferRepository->update($jobOffer);
                return new JsonResponse('Updated', 201);
            } catch (\PDOException $e) {
                return new JsonResponse($e->getMessage(), 500);
            }
        }
        return new JsonResponse($this->errorService->getErrorsJobOffer($jobOffer), 400);
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
        try {
            $jobOffers = $this->jobOfferRepository->list();
        } catch (\PDOException $e) {
            return new JsonResponse($e->getMessage(), 500);
        }

        if ($jobOffers) {
            $jobOffersJson = $this->serializer->serialize($jobOffers, 'json');
            return new JsonResponse($jobOffersJson, 200, [], true);
        }
        return new JsonResponse('No job offers found', 404);
    }
}