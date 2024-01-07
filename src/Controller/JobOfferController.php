<?php

namespace App\Controller;

use App\Services\EntityServices\JobOfferService;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\JobOfferRepository;
use App\Services\ErrorService;
use JsonException;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="JobOffer")
 */
class JobOfferController extends AbstractController
{
    private JobOfferRepository $jobOfferRepository;
    private SerializerInterface $serializer;
    private ErrorService $errorService;
    private JobOfferService $jobOfferService;

    public function __construct(
        JobOfferRepository $jobOfferRepository,
        SerializerInterface $serializer,
        ErrorService $errorService,
        JobOfferService $jobOfferService
    )
    {
        $this->jobOfferRepository = $jobOfferRepository;
        $this->serializer = $serializer;
        $this->errorService = $errorService;
        $this->jobOfferService = $jobOfferService;
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
     * @OA\Response(
     *     response=201,
     *     description="Job offer created",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Created"
     *    )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     *   )
     * )
     * @OA\RequestBody(
     *     request="JobOffer",
     *     description="Job offer to create",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="title",
     *     type="string",
     *     example="PHP developer"
     *    ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="PHP developer with 5 years of experience"
     *   ),
     *     @OA\Property(
     *     property="city",
     *     type="string",
     *     example="Paris"
     *  ),
     *     @OA\Property(
     *     property="salaryMin",
     *     type="integer",
     *     example="30000"
     * ),
     *     @OA\Property(
     *     property="salaryMax",
     *     type="integer",
     *     example="40000"
     * )
     * )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsJobOfferRequest($request) !== []) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsJobOfferRequest($request), JSON_THROW_ON_ERROR),
                400
            );
        }

        $jobOffer = $this->jobOfferService->buildJobOffer($request);

        try {
            $this->jobOfferRepository->create($jobOffer);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
        return new JsonResponse('Created', 201);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @throws JsonException
     * @OA\Response(
     *     response=200,
     *     description="Job offer found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="{
     *     ""id"": 1,
     *     ""title"": ""PHP developer"",
     *     ""description"": ""PHP developer with 5 years of experience"",
     *     ""city"": ""Paris"",
     *     ""salaryMin"": 30000,
     *     ""salaryMax"": 40000
     *     }"
     *   )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Job offer not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Job offer not found"
     *  )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the job offer",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function read(int $id): JsonResponse
    {
        try {
            $jobOffer = $this->jobOfferRepository->read($id);
            if (!$jobOffer) {
                throw new resourceNotFoundException(
                    json_encode('the job offer with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
                    404
                );
            }
        } catch (PDOException $e) {
            throw new databaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR),
                $e->getCode()
            );
        }

        return new JsonResponse($this->serializer->serialize($jobOffer, 'json'), 200, [], true);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws JsonException
     * @OA\Response(
     *     response=204,
     *     description="Job offer updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Updated"
     *  )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Job offer not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Job offer not found"
     * )
     * )
     * @OA\RequestBody(
     *     request="JobOffer",
     *     description="Job offer to update",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="title",
     *     type="string",
     *     example="PHP developer"
     * ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="PHP developer with 5 years of experience"
     * ),
     *     @OA\Property(
     *     property="city",
     *     type="string",
     *     example="Paris"
     * ),
     *     @OA\Property(
     *     property="salaryMin",
     *     type="integer",
     *     example="30000"
     * ),
     *     @OA\Property(
     *     property="salaryMax",
     *     type="integer",
     *     example="40000"
     * )
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the job offer",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsJobOfferRequest($request) !== []) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsJobOfferRequest($request), JSON_THROW_ON_ERROR),
                400
            );
        }
        $jobOffer = $this->jobOfferRepository->read($id);

        if ($jobOffer === false) {
            throw new ResourceNotFoundException(
                json_encode('The job offer with id ' . $id . ' was not found.', JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $jobOffer = $this->jobOfferService->updateJobOffer($jobOffer, $request);
            $this->jobOfferRepository->update($jobOffer);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse('Updated', 204);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws JsonException
     * @OA\Response(
     *     response=200,
     *     description="Job offer deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Job offer deleted"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Job offer not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Job offer not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the job offer",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $jobOffer = $this->jobOfferRepository->read($id);

        if ($jobOffer === false){
            throw new resourceNotFoundException(
                json_encode('the job offer with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $this->jobOfferRepository->delete($jobOffer);
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), $e->getCode());
        }

        return new JsonResponse('Job offer ' . $id . ' was deleted.', 200);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @throws JsonException
     * @OA\Response(
     *     response=200,
     *     description="Job offers found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Job offers found"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Job offers not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Job offers not found"
     * )
     * )
     */
    public function list(): JsonResponse
    {
        try {
            $jobOffers = $this->jobOfferRepository->list();
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }
        if( empty($jobOffers)){
            throw new ResourceNotFoundException(
                json_encode('no job offer found', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($jobOffers, 'json'), 200, [], true);
    }
}