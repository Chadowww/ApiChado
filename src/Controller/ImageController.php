<?php

namespace App\Controller;

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
          $uploadedFile->move($this->getParameter('images_directory'), $newFilename);

          return new JsonResponse(['message' => 'File uploaded with success!']);
      }

      return new JsonResponse(['message' => 'file not uploaded! :s']);
  }

  public function read(string $fileName): Response
  {
    $directory = $this->getParameter('images_directory');

    $filePath = $directory . $fileName;
    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Image not found.');
    }

    $file = new File($filePath);

    return $this->file($file);
  }

  public function update(string $fileName, Request $request): Response
  {
    $file = $this->read($fileName);

    $newFile = $request->files->get('file');

    if ($newFile) {
        $newFilename = uniqid('', true) . '.' . $newFile->guessExtension();
        $newFile->move($this->getParameter('images_directory'), $newFilename);
        $this->delete($fileName);

        return new JsonResponse(['message' => 'File uploaded with success!']);
    }
    return new JsonResponse(['message' => 'file not uploaded! :s']);
  }

  public function delete(string $fileName): Response
  {
    $directory = $this->getParameter('images_directory');

    $filePath = $directory . $fileName;
    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Image not found.');
    }

    unlink($filePath);

    return new JsonResponse(['message' => 'File deleted with success!']);
  }

  public function list(): Response
  {
    $directory = $this->getParameter('images_directory');

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
