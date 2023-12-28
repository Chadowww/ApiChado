<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

  public function read(int $id): Response
  {

  }

  public function update(int $id, Request $request): Response
  {

  }

  public function delete(int $id): Response
  {

  }

  public function list(): Response
  {

  }
}
