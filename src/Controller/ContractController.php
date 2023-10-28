<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Repository\ContractRepository;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ContractController extends AbstractController
{
    private ContractRepository $contractRepository;
    private SerializerInterface $serializer;
    private ErrorService $errorService;

    public function __construct(
        ContractRepository $contractRepository,
        SerializerInterface $serializer,
        ErrorService $errorService,
    )
    {
        $this->contractRepository = $contractRepository;
        $this->serializer = $serializer;
        $this->errorService = $errorService;
    }

    public function create(Request $request): JsonResponse
    {
        $contract = new Contract();
        $contract->setType($request->get('type'));
        if ($this->errorService->getErrorsContract($contract) === []) {
            $this->contractRepository->create($contract);
            return new JsonResponse('Created', 201);
        }
        return new JsonResponse($this->errorService->getErrorsContract($contract), 400);
    }

    public function read(): JsonResponse
    {
    }

    public function update(): JsonResponse
    {
    }

    public function delete(): JsonResponse
    {
    }

    public function list(): JsonResponse
    {
    }
}