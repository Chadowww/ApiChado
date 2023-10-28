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

    public function read(int $id): JsonResponse
    {
        $contract = $this->contractRepository->read($id);
        if(!$contract) {
            return new JsonResponse('id not found', 404);
        }
        $contractJson = $this->serializer->serialize($contract, 'json');
        return new JsonResponse($contractJson, 200, [], true);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $contract = $this->contractRepository->read($id);
        if(!$contract) {
            return new JsonResponse('id not found', 404);
        }
        $contract->setType($request->get('type'));
        if ($this->errorService->getErrorsContract($contract) === []) {
            $this->contractRepository->update($contract);
            return new JsonResponse('Updated', 200);
        }
        return new JsonResponse($this->errorService->getErrorsContract($contract), 400);
    }

    public function delete(int $id): JsonResponse
    {
        $contract = $this->contractRepository->read($id);
        if(!$contract) {
            return new JsonResponse('id not found', 404);
        }
        $this->contractRepository->delete($contract);
        return new JsonResponse('Deleted', 200);
    }

    public function list(): JsonResponse
    {
        $contract = $this->contractRepository->list();
        $contractJson = $this->serializer->serialize($contract, 'json');
        return new JsonResponse($contractJson, 200, [], true);
    }
}