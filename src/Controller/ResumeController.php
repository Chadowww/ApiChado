<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\ResumeRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\FileManagerService\FileManagerService;
use App\Services\RequestValidator\RequestValidatorService;
use Exception;
use JsonException;
use OpenApi\Annotations as OA;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Resume")
 */
class ResumeController extends AbstractController
{
    CONST string CV_DIRECTORY = 'CV_DIRECTORY';
    private RequestValidatorService $requestValidatorService;
    private EntityBuilder $entityBuilder;
    private ResumeRepository $resumeRepository;
    private SerializerInterface $serializer;
    private FileManagerService $fileManagerService;

    public function __construct(
        RequestValidatorService $requestValidatorService,
        EntityBuilder $entityBuilder,
        ResumeRepository $resumeRepository,
        SerializerInterface $serializer,
        FileManagerService $fileManagerService
    ) {
        $this->requestValidatorService = $requestValidatorService;
        $this->entityBuilder = $entityBuilder;
        $this->resumeRepository = $resumeRepository;
        $this->serializer = $serializer;
        $this->fileManagerService = $fileManagerService;
    }

    /**
     * @throws DatabaseException|JsonException|InvalidRequestException
     * @OA\Response(
     *     response=201,
     *     description="Resume created",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Created"
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
     * @OA\RequestBody(
     *     request="Resume",
     *     description="Resume to create",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="title", type="string", example="CV Développeur Web"),
     *     @OA\Property(property="file", type="file", example="cv-developpeur-web.pdf"),
     *     @OA\Property(property="candidateId", type="integer", example="1"),
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $resume = new Resume();
        $this->requestValidatorService->throwError400FromData($data, $resume);

        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $filename = $this->fileManagerService->upload($uploadedFile, self::CV_DIRECTORY);
            return new JsonResponse(['code' => '201', 'message' => 'File uploaded with success!', 'name' => $filename,]);
        }

        $resume = $this->entityBuilder->buildEntity($resume, $data);

        try {
            $this->resumeRepository->create($resume);
        } catch (Exception $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse('Resume created with success!', 201, [], true);
    }

    /**
     * @throws ResourceNotFoundException|JsonException
     * @OA\Response(
     *     response=200,
     *     description="Resume read",
     *     @OA\JsonContent(
     *     type="string",
     *     example="{
     *     ""id"": 1,
     *     ""title"": ""CV Développeur Web"",
     *     ""filename"": ""cv-developpeur-web.pdf"",
     *     ""createdAt"": ""2021-09-01T00:00:00+00:00"",
     *     ""updatedAt"": ""2021-09-01T00:00:00+00:00"",
     *     ""candidateId"": 1
     *     }"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Resume not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Resume not found!"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Resume id",
     *     required=true,
     *     @OA\Schema(type="integer", example="1")
     * )
     *
     */
    public function read(int $id): JsonResponse
    {
        $resume = $this->resumeRepository->read($id);

        if (!$resume) {
            throw new ResourceNotFoundException(
                json_encode(['error' => 'The resume with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($resume, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException|JsonException|DatabaseException|ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Resume updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Resume updated with success!"
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
     *     description="Resume not found",
     *     @OA\JsonContent(
     *     type="string",
     *      example="Resume not found!"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Database error",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Database error"
     * )
     * )
     * @OA\Response(
     *     response=512,
     *     description="File not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="File not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Resume id",
     *     required=true,
     *     @OA\Schema(type="integer", example="1")
     * )
     * @OA\RequestBody(
     *     request="Resume",
     *     description="Resume to update",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="title", type="string", example="CV Développeur Web"),
     *     @OA\Property(property="file", type="file", example="cv-developpeur-web.pdf"),
     *     @OA\Property(property="candidateId", type="integer", example="1"),
     *     )
     * )
     *
     */
    public function update(int $id, Request $request, ImageController $imageController): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $resume  = $this->resumeRepository->read($id);

        if (!$resume) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The resume with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->requestValidatorService->throwError400FromData($data, $resume);

        $fileName = json_decode($imageController->create($request)->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $data['filename'] = $fileName;

        $resume = $this->entityBuilder->buildEntity($resume, $data);

        try {
            $this->resumeRepository->update($resume);
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse('Resume updated with success!', 200, [], true);
    }

    /**
     * @throws DatabaseException|JsonException|ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Resume deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Resume deleted with success!"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Resume not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Resume not found!"
     * )
     * )
     * @OA\Parameter(
     *     name="filename",
     *     in="path",
     *     description="Resume filename",
     *     required=true,
     *     @OA\Schema(type="string", example="659d3e252779b4.10678192.pdf")
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $resume = $this->resumeRepository->read($id);

        if (!$resume) {
            throw new ResourceNotFoundException(
                json_encode(['error' => 'The resume with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }
        $fileName = $resume->getFilename();

        if ($this->fileManagerService->verifyExistFile($fileName, 'CV_DIRECTORY') === false) {
            throw new ResourceNotFoundException(json_encode(['Resume not found!'], JSON_THROW_ON_ERROR), 404);
        }

        $this->fileManagerService->delete($fileName, 'CV_DIRECTORY');

        try {
            $resumeDeleted = $this->resumeRepository->delete($id);

            if ($resumeDeleted === false) {
                throw new resourceNotFoundException(
                    json_encode(['error' => 'The apply with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                    404
                );
            }
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse('Resume deleted with success!', 200, [], true);
    }

    /**
     * @throws JsonException|ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Resume list",
     *     @OA\JsonContent(
     *     type="string",
     *     example="[
     *     {
     *     ""id"": 1,
     *     ""title"": ""CV Développeur Web"",
     *     ""filename"": ""cv-developpeur-web.pdf"",
     *     ""createdAt"": ""2021-09-01T00:00:00+00:00"",
     *     ""updatedAt"": ""2021-09-01T00:00:00+00:00"",
     *     ""candidateId"": 1
     *     },
     *     {
     *     ""id"": 2,
     *     ""title"": ""CV Développeur Web"",
     *     ""filename"": ""cv-developpeur-web.pdf"",
     *     ""createdAt"": ""2021-09-01T00:00:00+00:00"",
     *     ""updatedAt"": ""2021-09-01T00:00:00+00:00"",
     *     ""candidateId"": 1
     *     }
     *     ]"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Database error",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Database error"
     * )
     * )
     */
    public function list(): JsonResponse
    {
        $resumes = $this->resumeRepository->list();

        if (!$resumes) {
            throw new resourceNotFoundException(
                json_encode('Resumes list was not found', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($resumes, 'json'), 200, [], true);
    }


    /**
     * @param $id
     * @return JsonResponse
     * @throws JsonException|ResourceNotFoundException
     */
    public function getResumesByCandidate($id): JsonResponse
    {
        $resumes = $this->resumeRepository->findByCandidate($id);

        if (!$resumes) {
            throw new resourceNotFoundException(
                json_encode('Resumes list was not found', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($resumes, 'json'), 200, [], true);
    }
}