<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ImageController extends AbstractController
{
    public function create(Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $newFilename = uniqid('', true) . '.' . $uploadedFile->guessExtension();

            if (mime_content_type($uploadedFile->getRealPath()) === 'application/pdf') {
                $uploadedFile->move($this->getParameter('cv.directory'), $newFilename);
            } else {
                $uploadedFile->move($this->getParameter('images.directory'), $newFilename);
            }

            return new JsonResponse(['code' => '201', 'message' => 'File uploaded with success!', 'name' => $newFilename,]);
        }
        return new JsonResponse(['message' => 'file not uploaded! :s']);
    }

    public function read(string $fileName): Response
    {
        $directory = $this->getParameter('images.directory');

        $filePath = $directory . $fileName;
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Image not found.');
        }

        $file = new File($filePath);

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
            $newFilename = uniqid('', true) . '.' . $newFile->guessExtension();
            $newFile->move($this->getParameter('images.directory'), $newFilename);
            $this->delete($fileName);

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
        $directory = $this->getParameter('images.directory');

        $filePath = $directory . $fileName;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Image not found.');
        }

        unlink($filePath);

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
        $directory = $this->getParameter('images.directory');

        $files = scandir($directory);

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
