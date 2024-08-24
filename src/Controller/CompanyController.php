<?php

namespace App\Controller;

use App\Entity\Company;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\CompanyRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use JsonException;
use PDOException;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Company")
 */
class CompanyController extends AbstractController
{
    /**
     * @param RequestValidatorService $requestValidatorService
     * @param EntityBuilder $entityBuilder
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly RequestValidatorService $requestValidatorService,
        private readonly EntityBuilder           $entityBuilder,
        private readonly CompanyRepository       $companyRepository,
        private readonly SerializerInterface     $serializer
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
     * @OA\Response(
     *     response=201,
     *     description="Company created",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Created"
     *   )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     *  )
     * )
     * @OA\RequestBody(
     *     request="Company",
     *     description="Company object that needs to be added to the database",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Company name"
     *  ),
     *     @OA\Property(
     *     property="phone",
     *     type="string",
     *     example="Company phone"
     * ),
     *     @OA\Property(
     *     property="address",
     *     type="string",
     *     example="Company address"
     * ),
     *     @OA\Property(
     *     property="city",
     *     type="string",
     *     example="Company city"
     * ),
     *     @OA\Property(
     *     property="country",
     *     type="string",
     *     example="Company country"
     * ),
     *     @OA\Property(
     *     property="siret",
     *     type="string",
     *     example="Company siret code"
     * ),
     *     @OA\Property(
     *     property="userId",
     *     type="integer",
     *     example="Company user id"
     * )))
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $company = new Company();

        $this->requestValidatorService->throwError400FromData($data, $company);

        $company = $this->entityBuilder->buildEntity($company, $data);

        $this->companyRepository->create($company);

        return new JsonResponse(['message' => 'Company created successfully'], 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws DatabaseException
     * @throws JsonException
     * @throws ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Company found",
     *     @OA\JsonContent(
     *    type="string",
     *     example="Company found"
     * ))
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     * ))
     * @OA\Response(
     *     response=404,
     *     description="Company not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Company not found"
     * ))
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the company to read",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * ))
     */
    public function read(int $id): JsonResponse
    {
        $company = $this->companyRepository->read($id);

        if (!$company) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The company with id ' . $id . ' does not exist.'],JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($company, 'json'), 200, [], true);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
     * @throws ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Company updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Updated"
     * )
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
     *     description="Company not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Company not found"
     * )
     * )
     * @OA\RequestBody(
     *     request="Company",
     *     description="Company object that needs to be updated in the database",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Company name"
     * ),
     *     @OA\Property(
     *     property="phone",
     *     type="string",
     *     example="Company phone"
     * ),
     *     @OA\Property(
     *     property="address",
     *     type="string",
     *     example="Company address"
     * ),
     *     @OA\Property(
     *     property="city",
     *     type="string",
     *     example="Company city"
     * ),
     *     @OA\Property(
     *     property="country",
     *     type="string",
     *     example="Company country"
     * ),
     *     @OA\Property(
     *     property="siret",
     *     type="string",
     *     example="Company siret code"
     * ),
     *     @OA\Property(
     *     property="userId",
     *     type="integer",
     *     example="Company user id"
     * )))
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $company = $this->companyRepository->read($id);

        if (!$company){
            throw new ResourceNotFoundException(
                json_encode('The company with id ' . $id . ' does not exist.', JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->requestValidatorService->throwError400FromData($data, $company);

        $company = $this->entityBuilder->buildEntity($company, $data);

        try {
            $this->companyRepository->update($company);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse(['response' => 'company updated'], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws DatabaseException
     * @throws JsonException
     * @throws ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Company deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Deleted"
     * )
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
     *     description="Company not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Company not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the company to delete",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        if (!$this->companyRepository->read($id)) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The candidate with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->companyRepository->delete($id);

        return new JsonResponse(['response' => 'company deleted'], 200);
    }

    /**
     * @throws ResourceNotFoundException|DatabaseException|JsonException
     * @return JsonResponse
     * @OA\Response(
     *     response=200,
     *     description="List of companies",
     *     @OA\JsonContent(
     *     type="string",
     *     example="List of companies"
     * )
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
     *     description="Company not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Company not found"
     * )
     * )
     */
    public function list(): JsonResponse
    {
        $companies = $this->companyRepository->list();

        if (!$companies) {
            throw new ResourceNotFoundException(
                json_encode('Candidates not found in database', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($companies, 'json'), 200, [], true);
    }


    /**
     * @throws ResourceNotFoundException|JsonException
     * @throws DatabaseException
     * @return JsonResponse
     * @OA\Response(
     *     response=200,
     *     description="List of companies",
     *     @OA\JsonContent(
     *     type="string",
     *     example="List of companies"
     * )
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
     *     description="Company not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Company not found"
     * )
     * )
     */
    public function topOffers(): JsonResponse
    {
        $companies = $this->companyRepository->topOffers();

        if (!$companies) {
            throw new ResourceNotFoundException(
                json_encode('Candidates not found in database', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($companies, 'json'), 200, [], true);
    }
}