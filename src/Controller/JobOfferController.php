<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\JobOfferRepository;
use JsonException;
use OpenApi\Annotations as OA;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="JobOffer")
 */
class JobOfferController extends AbstractController
{
    public function __construct(
        private readonly RequestValidatorService $requestValidatorService,
        private readonly EntityBuilder           $entityBuilder,
        private readonly JobOfferRepository      $jobOfferRepository,
        private readonly SerializerInterface     $serializer
    ) {}

    /**
     * @throws DatabaseException|InvalidRequestException|JsonException
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
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $jobOffer = new JobOffer();

        $this->requestValidatorService->throwError400FromData($data, $jobOffer);

        $jobOffer = $this->entityBuilder->buildEntity($jobOffer, $data);

        $this->jobOfferRepository->create($jobOffer);

        return new JsonResponse(['message' => 'Job Offer created successfully'], 201);
    }

    /**
     * @throws ResourceNotFoundException|JsonException
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
        $jobOffer = $this->jobOfferRepository->read($id);

        if ($jobOffer === false) {
            throw new ResourceNotFoundException(
                json_encode('The job offer with id ' . $id . ' does not exist.', JSON_THROW_ON_ERROR),
                404
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
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $jobOffer = $this->jobOfferRepository->read($id);

        if (!$jobOffer) {
            throw new ResourceNotFoundException(
                json_encode('The job offer with id ' . $id . ' does not exist.', JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->requestValidatorService->throwError400FromData($data, $jobOffer);

        $jobOffer = $this->entityBuilder->buildEntity($jobOffer, $data);

        $this->jobOfferRepository->update($jobOffer);

        return new JsonResponse(['message' => 'Job offer updated successfully'], 200);
    }

    /**
     * @throws DatabaseException|ResourceNotFoundException|JsonException
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
        if (!$this->jobOfferRepository->read($id)){
            throw new resourceNotFoundException(
                json_encode('the job offer with id ' . $id . ' does not exist.', JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->jobOfferRepository->delete($id);

        return new JsonResponse(['message' => 'Job offer deleted successfully'], 200);
    }

    /**
     * @throws ResourceNotFoundException|JsonException
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
        $jobOffer = $this->jobOfferRepository->list();

        if (!$jobOffer) {
            throw new ResourceNotFoundException(
                json_encode('No job offers found', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($jobOffer, 'json'), 200, [], true);
    }
}