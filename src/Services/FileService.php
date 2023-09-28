<?php

namespace App\Service;

use Exception;
use Intervention\Image\ImageManager;
use Predis\Client;
use App\Kernel;
use DateTime;
use DateTimeZone;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\SluggerInterface;
use \Symfony\Component\HttpFoundation\File\File;


class FileService
{
    private string $baseDir;
    private ImageManager $imageManager;
    private $attachmentImportDir;
    private string $entryImportDir;


    public function __construct(
        private $entryImportDirName,
        private $attachmentImportDirName,
        private Kernel $kernel,
        private SluggerInterface $slugger,
        private Filesystem $filesystem,
        private UtilitiesService $utilitiesService
    ) {
        $this->imageManager =  new ImageManager();
        //// FS
        $this->baseDir = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . "uploads";
        $this->attachmentImportDir = $this->baseDir . DIRECTORY_SEPARATOR . $this->attachmentImportDirName;
        $this->entryImportDir = $this->baseDir . DIRECTORY_SEPARATOR . $this->entryImportDirName;
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    private function deleteEmptyDirectory($path)
    {
        if (!is_dir($path)) {
            return;
        }
        $dir = scandir($path);


        if (count($dir) < 3) {
            return rmdir($path);
        }
    }

    public function deleteFile(string $path): bool
    {
        if (!file_exists($path))
            return false;
        return unlink($path);
    }


    public function uploadFile(UploadedFile $file, string $dir, string $name = null): FileException | File
    {
        return $file->move($dir, $name);
    }

    public function checkMimeType(UploadedFile $file, $allowedMimeTypes = ['image/png', 'image/jpeg', 'application/pdf']): bool
    {
        $fileMimeType = $file->getClientMimeType();
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            return false;
        } else {
            return true;
        }
    }


    public function checkFileSize(UploadedFile $file, $size = 100): bool
    {
        return false;
    }
    public function pathWithDirectorySystem(string $path, $sep = '/'): string
    {
        return str_replace($sep, DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Assure l'existence d'un répertoire en le créant s'il n'existe pas déjà.
     *
     * @param string $path Le chemin du répertoire à assurer.
     * @param string $sep Le séparateur de répertoire à remplacer dans le chemin. Par défaut, c'est '/'.
     * @return string Le chemin du répertoire avec le séparateur de répertoire approprié pour le système d'exploitation.
     */
    public function createDirectoryIfMissing(string $path, string $sep = '/'): string
    {
        $pathWithOsSeparator = $this->pathWithDirectorySystem($path, $sep);

        if (!is_dir($pathWithOsSeparator)) {
            $this->filesystem->mkdir($pathWithOsSeparator, 0700);
        }
        return $pathWithOsSeparator;
    }

    /**
     * @throws Exception
     */
    public function getRandomNameFile(string $prefix = null, string $extension = null): string
    {
        $timestamp = date_format(new DateTime("now", new DateTimeZone("Europe/Paris")), 'Y-m-d-H-i-s');
        $randomString = bin2hex(random_bytes(8));

        $randomName = ($prefix ?: '') . '_' . $timestamp . '_' . $randomString;

        if ($extension !== null) {
            $randomName .= '.' . ltrim($extension, '.');
        }

        return $randomName;
    }

    /**
     * @throws Exception
     */
    public function createFile(string $path, string $content, string $extension  = '', string $prefixNameFile = null, string $nameFile = null): string
    {
        $directory = $this->createDirectoryIfMissing($this->baseDir . DIRECTORY_SEPARATOR . $path);
        if ($nameFile === null)
            $nameFile = $this->getRandomNameFile($prefixNameFile, $extension);

        $filePath = $directory . DIRECTORY_SEPARATOR . $nameFile;
        $this->filesystem->dumpFile($filePath, $content);
        return $filePath;
    }



    /**
     * createThumbnailPicture uses the Intervention Image library to create a thumbnail of a given image file.
     *
     * @see : https://image.intervention.io/v2/api/make make documentation
     * @param File $resource The file resource to create a thumbnail from.
     */

    public function createThumbnailPicture(File $resource, int $widthDesired, int $heightDesired = null, \Closure $callback = null, string $position = 'center'): void
    {
        $pathPicture    = $resource->getPath() . DIRECTORY_SEPARATOR;
        $picture        = $this->imageManager->make($pathPicture . $resource->getFilename());
        $basename       = $resource->getBasename('.' . $resource->getExtension());
        $pathNewFile    = $pathPicture . "thumbnail_"  . $basename . ".jpg";

        $e = $picture->fit($widthDesired, $heightDesired, $callback, $position)->encode('jpg', 100)->save($pathNewFile);
    }


    /**
     * The safe extensions associated with the MIME type by checking if the file signature is actually the one associated with the MIME.
     *
     * @param string $pathFile The path to the file.
     * @return array|false The extension(s) corresponding to the MIME type, or false if the MIME type is not a valid image or the file signature is not consistent with the MIME type.
     */
    public function getSafeExtensions(string $pathFile): array | false
    {
        $mimeType = $this->isImage($pathFile);
        if ($mimeType) {
            $mimeTypes = new MimeTypes();
            return $mimeTypes->getExtensions($mimeType);
        }
        return false;
    }
    /**
     * Checks whether the MIME type of a file corresponds to that of a valid image by verifying
     * the file signature, and if so, returns the MIME type of the image.
     * @param string $pathFile The path to the file.
     * @return string|false Returns the MIME type of the image if the MIME type corresponds to a valid image and the file signature is valid, or false otherwise.
     */
    public function isImage(string $pathFile): string | false
    {
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($pathFile);
        $image = match ($mimeType) {
            'image/png'  => imagecreatefrompng($pathFile),
            'image/jpeg' => imagecreatefromjpeg($pathFile),
            default => false,
        };

        if ($image)
            imagedestroy($image); // free memory image
        return ($image ? $mimeType : false);
    }
}
