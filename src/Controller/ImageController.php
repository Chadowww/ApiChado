<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\FileManagerService\FileManagerService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ImageController extends AbstractController
{
    CONST string IMAGES_DIRECTORY = 'IMAGES_DIRECTORY';
    private FileManagerService $fileManagerService;

    public function __construct(FileManagerService $FileManagerService)
    {
        $this->fileManagerService = $FileManagerService;
    }

    /**
     * @throws DatabaseException
     */
    public function create(Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $filename = $this->fileManagerService->upload($uploadedFile, self::IMAGES_DIRECTORY);
            return new JsonResponse(['code' => '201', 'message' => 'File uploaded with success!', 'name' => $filename,]);
        }

        return new JsonResponse(['message' => 'file not uploaded! :s']);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function read(string $fileName): Response
    {
        $this->fileManagerService->verifyExistFile($fileName, self::IMAGES_DIRECTORY);

        $file = new File($this->fileManagerService->getFilePath($fileName, self::IMAGES_DIRECTORY));

        return $this->file($file);
    }

    /**
     * Updates a file with a new one.
     *
     * @param string $fileName The name of the file to be updated.
     * @param Request $request The Request object containing the new file.
     *
     * @return Response The response indicating the success or failure of the update.
     * @throws \Exception
     *@OA\Response(
     *     response=200,
     *     description="Returns the success message",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="message", type="string", example="File uploaded with success!"),
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Returns the error message",
     *     @OA\JsonContent(
     *     type="string",
     *     example="File not found."
     *    )
     * )
     * @OA\Parameter(
     *     name="fileName",
     *     in="path",
     *     description="The name of the file to be updated.",
     *     required=true,
     *     @OA\Schema(type="string")
     * )
     */
    public function update(string $fileName, Request $request): Response
    {
        $file = $this->read($fileName);

        $newFile = $request->files->get('file');

        if ($newFile) {
            $this->fileManagerService->upload($newFile, self::IMAGES_DIRECTORY);
            $this->fileManagerService->delete($fileName, self::IMAGES_DIRECTORY);
            return new JsonResponse(['message' => 'File uploaded with success!']);
        }
        return new JsonResponse(['message' => 'file not uploaded! :s']);
    }

    /**
     * @param string $fileName
     * @return Response
     * @throws \Exception
     * @OA\Response(
     *     response=200,
     *     description="Returns the success message",
     *     @OA\JsonContent(
    *      type="string",
     *     example="File deleted with success!"
     *   )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Returns the error message",
     *     @OA\JsonContent(
     *     type="string",
     *     example="File not found."
     *   )
     * )
     * @OA\Parameter(
     *     name="fileName",
     *     in="path",
     *     description="The name of the file to be deleted.",
     *     required=true,
     *     @OA\Schema(type="string")
     * )
     */
    public function delete(string $fileName): Response
    {
        if ($this->fileManagerService->verifyExistFile($fileName, self::IMAGES_DIRECTORY)) {
            $this->fileManagerService->delete($fileName, self::IMAGES_DIRECTORY);
        }

        return new JsonResponse(['message' => 'File deleted with success!']);
    }

    /**
     * @return Response
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of images",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(property="name", type="string", example="image.jpg"),
     *     @OA\Property(property="url", type="string", example="http://localhost:8000/images/image.jpg"),
     *     )
     *   )
     * )
     * )
     */
    public function list(): Response
    {
        $files = scandir(FileManagerService::IMAGES_DIRECTORY);

        $images = [];
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $images[] = [
                'name' => $file,
                'url' => $this->generateUrl('ImageRead', ['fileName' => $file]),
            ];
        }

        return new JsonResponse($images);
    }
}
