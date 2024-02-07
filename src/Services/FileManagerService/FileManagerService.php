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
     * @var string
     * @Autowire("%images.directory%")
     */
    private string $imagesDirectory;
    /**
     * @var string
     * @Autowire("%cv.directory%")
     */
    private string $cvDirectory;

    /**
     * @param string $imagesDirectory
     * @param string $cvDirectory
     * @Autowire("%images.directory%")
     * @Autowire("%cv.directory%")
     */
    public function __construct(
        #[Autowire('%images.directory%')] string $imagesDirectory,
        #[Autowire('%cv.directory%')] string $cvDirectory
    ) {
        $this->imagesDirectory = $imagesDirectory;
        $this->cvDirectory = $cvDirectory;
    }

    /**
     * @param string $flag
     * @return string
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
     * @param UploadedFile $file
     * @param string $flag
     * @return string
     * @throws DatabaseException
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
     * @param string $filePath
     * @param string $flag
     * @return bool
     */
    public function delete(string $filName, string $flag): bool
    {
        $filePath = $this->determineDirectory($flag) . $filName;
        return file_exists($filePath) && unlink($filePath);
    }

    /**
     * @param string $fileName
     * @param string $flag
     * @return bool
     * @throws ResourceNotFoundException
     */
    public function verifyExistFile(string $fileName, string $flag): bool
    {
        dd($fileName);
        $filePath = $this->determineDirectory($flag) . $fileName;
        if (!file_exists($filePath)) {
            throw new ResourceNotFoundException('File not found.', 404);
        }
        return true;
    }

    public function getFilePath(string $fileName, string $flag): string
    {
        return $this->determineDirectory($flag) . $fileName;
    }
}