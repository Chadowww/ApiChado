<?php

namespace App\Controller;

use App\Exceptions\InvalidRequestException;
use App\Repository\CompanyRepository;
use App\Services\EntityServices\CompanyService;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Company")
 */
class CompanyController extends AbstractController
{
    private ErrorService $errorService;
    private CompanyService $companyService;
    private CompanyRepository $companyRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ErrorService $errorService,
        CompanyService $companyService,
        CompanyRepository $companyRepository,
        SerializerInterface $serializer
    )
    {
        $this->errorService = $errorService;
        $this->companyService = $companyService;
        $this->companyRepository = $companyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws \JsonException
     * @throws InvalidRequestException
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
     *     property="user_id",
     *     type="integer",
     *     example="Company user id"
     * )))
     */
    public function create(Request $request): JsonResponse
    {
        if($this->errorService->getErrorsCompanyRequest($request) !== []) {
           throw new InvalidRequestException(json_encode($this->errorService->getErrorsCompanyRequest($request),
                JSON_THROW_ON_ERROR), 400);
        }
        $company = $this->companyService->buildCompany($request);

        try {
            $this->companyRepository->create($company);
        } catch (\Exception $e) {
            throw new InvalidRequestException($e->getMessage(), 400);
        }

        return new JsonResponse(['201' => 'new company created'], 201);
    }

    public function read()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    public function list()
    {
    }
}