<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\JobOfferRepository;
use App\Services\ErrorService;
use PDOException;
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

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function create(Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsJobOfferRequest($request) !== []) {
            throw new InvalidRequestException(json_encode($this->errorService->getErrorsJobOfferRequest($request), JSON_THROW_ON_ERROR), 400);
        }

        $jobOffer = new JobOffer();
        $jobOffer->setTitle($request->get('title'));
        $jobOffer->setDescription($request->get('description'));
        $jobOffer->setCity($request->get('city'));
        $jobOffer->setSalaryMin($request->get('salaryMin'));
        $jobOffer->setSalaryMax($request->get('salaryMax'));

        try {
            $this->jobOfferRepository->create($jobOffer);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
        return new JsonResponse('Created', 201);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     */
    public function read(int $id): JsonResponse
    {
        try {
            $jobOffer = $this->jobOfferRepository->read($id);
            if (!$jobOffer) {
                throw new resourceNotFoundException(json_encode('the job offer with id ' . $id . ' was not found', JSON_THROW_ON_ERROR), 404);
            }
        } catch (PDOException $e) {
            throw new databaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), $e->getCode());
        }

        $jobOfferJson = $this->serializer->serialize($jobOffer, 'json');
        return new JsonResponse($jobOfferJson, 200, [], true);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     * @throws ResourceNotFoundException
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsJobOfferRequest($request) !== []) {
            throw new InvalidRequestException(json_encode($this->errorService->getErrorsJobOfferRequest($request), JSON_THROW_ON_ERROR),
                400);
        }

        $jobOffer = $this->jobOfferRepository->read($id);

        if ($jobOffer === false) {
            throw new ResourceNotFoundException(json_encode('The job offer with id ' . $id . ' was not found.', JSON_THROW_ON_ERROR), 404);
        }

        try {
            $jobOffer->setTitle($request->get('title'));
            $jobOffer->setDescription($request->get('description'));
            $jobOffer->setCity($request->get('city'));
            $jobOffer->setSalaryMin($request->get('salaryMin'));
            $jobOffer->setSalaryMax($request->get('salaryMax'));
            $this->jobOfferRepository->update($jobOffer);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse('Updated', 200);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function delete(int $id): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);

        if ($jobOffer === false){
            throw new resourceNotFoundException(json_encode('the job offer with id ' . $id . ' was not found', JSON_THROW_ON_ERROR), 404);
        }

        try {
            $this->jobOfferRepository->delete($jobOffer);
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), $e->getCode());
        }

        return new JsonResponse('Job offer ' . $id . ' was deleted.');
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function list(): JsonResponse
    {
        try {
            $jobOffers = $this->jobOfferRepository->list();
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

       if($jobOffers === false){
           throw new ResourceNotFoundException(json_encode('no job offer found', JSON_THROW_ON_ERROR), 404);
       }

       return new JsonResponse($jobOffers, 200);
    }
}