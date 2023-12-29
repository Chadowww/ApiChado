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
    error_log($filePath);
    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Image not found.');
    }

    $file = new File($filePath);

    return $this->file($file);
  }

  public function update(string $name, Request $request): Response
  {

  }

  public function delete(int $id): Response
  {

  }

  public function list(): Response
  {

  }
}
