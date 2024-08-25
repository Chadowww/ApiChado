<?php

namespace App\Services\FileManagerService;

use App\Exceptions\DatabaseException;
use App\Exceptions\ResourceNotFoundException;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManagerService
{
    /**
     * @var string
     */
    const string IMAGES_DIRECTORY = 'IMAGES_DIRECTORY';
    /**
     * @var string
     */
    const string CV_DIRECTORY = 'CV_DIRECTORY';

    /**
     * @param string $imagesDirectory
     * @param string $cvDirectory
     * @Autowire("%images.directory%")
     * @Autowire("%cv.directory%")
     */
    public function __construct(
        #[Autowire('%images.directory%')] private readonly string $imagesDirectory,
        #[Autowire('%cv.directory%')] private readonly string $cvDirectory
    ) {}

    /**
     * Determine the directory to upload the file
     * @param string $flag
     * @return string
     * @throws InvalidArgumentException
     */
    private function determineDirectory(string $flag): string
    {
        return match ($flag) {
            self::IMAGES_DIRECTORY => $this->imagesDirectory,
            self::CV_DIRECTORY => $this->cvDirectory,
            default => throw new InvalidArgumentException("Invalid directory flag"),
        };
    }

    /**
     * Upload a file to a specific directory.
     * @param UploadedFile $file The file to upload.
     * @param string $flag The flag indicating the directory to upload to.
     * - FileManagerService::IMAGES_DIRECTORY for the images directory
     * - FileManagerService::CV_DIRECTORY for the CV directory
     * @return string The name of the uploaded file.
     * @throws DatabaseException If an error occurs during the upload.
     */
    public function upload(UploadedFile $file, string $flag): string
    {
        $fileName = uniqid('', true) . '.' . $file->guessExtension();
        $directory = $this->determineDirectory($flag);

        if (!$file->move($directory, $fileName)) {
            throw new DatabaseException('Error uploading the file.', 500);
        }

        return $fileName;
    }

    /**
     * Delete a file from a specific directory.
     *
     * @param string $filName
     * @param string $flag The flag indicating the directory to delete from.
     * - FileManagerService::IMAGES_DIRECTORY for the images directory
     * - FileManagerService::CV_DIRECTORY for the CV directory
     * @return bool True if the file was successfully deleted, false otherwise.
     */
    public function delete(string $filName, string $flag): bool
    {
        $filePath = $this->determineDirectory($flag) . $filName;
        return file_exists($filePath) && unlink($filePath);
    }

    /**
     * Verify if a file exists in a specific directory.
     *
     * @param string $fileName The name of the file to verify.
     * @param string $flag The flag indicating the directory to verify in.
     *  - FileManagerService::IMAGES_DIRECTORY for the images directory
     *  - FileManagerService::CV_DIRECTORY for the CV directory
     * @return bool True if the file exists, false otherwise.
     * @throws ResourceNotFoundException If the file does not exist.
     */
    public function verifyExistFile(string $fileName, string $flag): bool
    {
        $filePath = $this->determineDirectory($flag) . $fileName;
        if (!file_exists($filePath)) {
            throw new ResourceNotFoundException('File not found.', 404);
        }
        return true;
    }

    /**
     * Get the path of a file in a specific directory.
     *
     * @param string $fileName The name of the file to get the path of.
     * @param string $flag The flag indicating the directory to get the path from.
     * - FileManagerService::IMAGES_DIRECTORY for the images directory
     * - FileManagerService::CV_DIRECTORY for the CV directory
     * @return string The path of the file.
     */
    public function getFilePath(string $fileName, string $flag): string
    {
        return $this->determineDirectory($flag) . $fileName;
    }
}