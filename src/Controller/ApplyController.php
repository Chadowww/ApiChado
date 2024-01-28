<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\ApplyRepository;
use App\Services\EntityServices\ApplyService;
use App\Services\ErrorService;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(name="Apply")
 */
class ApplyController extends AbstractController
{
    /**
     * @var ErrorService
     */
    private ErrorService $errorService;
    /**
     * @var ApplyService
     */
    private ApplyService $applyService;
    /**
     * @var ApplyRepository
     */
    private ApplyRepository $applyRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param ErrorService $errorService
     * @param ApplyService $applyService
     * @param ApplyRepository $applyRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ErrorService $errorService,
        ApplyService $applyService,
        ApplyRepository $applyRepository,
        SerializerInterface $serializer
    ) {
        $this->errorService = $errorService;
        $this->applyService = $applyService;
        $this->applyRepository = $applyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws \JsonException
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @OA\RequestBody(
     *      request="JobOffer",
     *      description="Job offer to create",
     *      required=true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *              property="candidate_id",
     *              type="integer",
     *              example=2
     *          ),
     *          @OA\Property(
     *              property="resume_id",
     *              type="integer",
     *              example=7
     *          ),
     *          @OA\Property(
     *              property="joboffer_id",
     *              type="integer",
     *              example=2
     *          ),
     *          @OA\Property(
     *              property="status",
     *              type="string",
     *              example="denied | pending | accepted"
     *          ),
     *          @OA\Property(
     *              property="message",
     *              type="string",
     *              example="Message de candidature"
     *          )
     *      )
     * )
     * @OA\Response(
     *       response=400,
     *       description="An error was found in request",
     *       @OA\JsonContent(
     *           type="string",
     *           example="Request must contain status, candidate_id, resume_id and joboffer_id fields"
     *       )
     *  )
     * @OA\Response(
     *      response=201,
     *      description="Apply created.",
     *      @OA\JsonContent(
     *          type="string",
     *          example="Created"
     *      )
     * )
     **/
    public function create(Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsApplyRequest($request)) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsApplyRequest($request), JSON_THROW_ON_ERROR,),
                400
            );
        }

        $apply = $this->applyService->createApply($request);

        try {
            $this->applyRepository->create($apply);
        } catch (Exception $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse(['message' => 'Apply created successfully', 'status' => '201']);
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="Apply found",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="apply_id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="status",
     *     type="string",
     *     example="denied | pending | accepted"
     *    ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Message de candidature"
     *   ),
     *     @OA\Property(
     *     property="candidate_id",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="resume_id",
     *     type="integer",
     *     example=7
     *     ),
     *     @OA\Property(
     *     property="joboffer_id",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     *    ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     *   )
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while retrieving the apply",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while retrieving the apply"
     *    )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply not found"
     *   )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Apply id",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example=1
     *     )
     * )
     */
    public function read(int $id): JsonResponse
    {
        try {
            $apply = $this->applyRepository->read($id);
            if (!$apply) {
                throw new resourceNotFoundException(
                    json_encode('the apply with id' . $id . ' was not found', JSON_THROW_ON_ERROR),
                    404
                );            }
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                500
            );
        }

        return new JsonResponse($this->serializer->serialize($apply, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException
     * @throws JsonException
     * @throws Exception
     * @OA\RequestBody(
     *     request="JobOffer",
     *     description="Job offer to update",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="status",
     *     type="string",
     *     example="denied | pending | accepted"
     *   ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Message de candidature"
     *  ),
     *     @OA\Property(
     *     property="candidate_id",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="resume_id",
     *     type="integer",
     *     example=7
     *     ),
     *     @OA\Property(
     *     property="joboffer_id",
     *     type="integer",
     *     example=2
     *     )
     * )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Apply updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply updated"
     *  )
     * )
     * @OA\Response(
     *     response=400,
     *     description="An error was found in request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Request must contain status, candidate_id, resume_id and joboffer_id fields"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while updating the apply",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while updating the apply"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Apply id",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example=1
     *     )
     * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsApplyRequest($request)) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsApplyRequest($request), JSON_THROW_ON_ERROR,),
                400
            );
        }

        $apply = $this->applyRepository->read($id);

        $apply = $this->applyService->updateApply($request, $apply);

        try {
            $this->applyRepository->update($apply);
            return new JsonResponse(['message' => 'Apply updated successfully', 'status' => '200']);
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                500
            );
        }
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     * @throws Exception
     * @OA\Response(
     *     response=200,
     *     description="Apply deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply deleted"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while deleting the apply",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while deleting the apply"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Apply id",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example=1
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->applyRepository->delete($id);
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                400
            );
        }
    }

    /**
     * @return JsonResponse
     * @throws DatabaseException
     * @throws JsonException
     * @OA\Response(
     *     response=200,
     *     description="Apply list",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(
     *     property="apply_id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="status",
     *     type="string",
     *     example="denied | pending | accepted"
     *   ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Message de candidature"
     * ),
     *     @OA\Property(
     *     property="candidate_id",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="resume_id",
     *     type="integer",
     *     example=7
     *     ),
     *     @OA\Property(
     *     property="joboffer_id",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     *   ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     * )
     * )
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while retrieving the apply list",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while retrieving the apply list"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply list not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply list not found"
     * )
     * )
     */
    public function list(): JsonResponse
    {
        try {
            $applies = $this->applyRepository->list();
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                500
            );
        }
        return new JsonResponse($this->serializer->serialize($applies, 'json'), 200, [], true);
    }
}